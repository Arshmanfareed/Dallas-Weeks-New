<?php

namespace App\Http\Controllers;

use App\Models\ImportedLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CsvController extends Controller
{
    function import_csv(Request $request)
    {
        if (Auth::check()) {
            $validated = $request->validate([
                'campaign_url' => 'required|file|mimes:csv,txt'
            ]);
            $user_id = Auth::user()->id;
            if ($_FILES['campaign_url']['error'] === UPLOAD_ERR_OK) {
                $file = $request->file('campaign_url');
                $fileType = $file->getClientMimeType();
                if ($fileType == 'text/csv' || $fileType == 'application/vnd.ms-excel') {
                    $uploadDir = 'uploads/';
                    $extension = $file->getClientOriginalExtension();
                    $fileName = 'imported_leads_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $uploadFilePath = $file->storeAs($uploadDir, $fileName);
                    if ($uploadFilePath) {
                        $fileHandle = fopen(storage_path('app/' . $uploadFilePath), 'r');
                        if ($fileHandle !== false) {
                            $csvData = [];
                            $delimiter = ',';
                            $enclosure = '"';
                            $escape = '\\';
                            $columnNames = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape);
                            foreach ($columnNames as $colName) {
                                $csvData[$colName] = [];
                            }
                            while (($rowData = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape)) !== false) {
                                foreach ($columnNames as $index => $colName) {
                                    $csvData[$colName][] = $rowData[$index] ?? null;
                                }
                            }
                            $count = 0;
                            $has_url_or_email = false;
                            $total = 0;
                            $total_Urls = [];
                            $duplicates = 0;
                            $duplicate_Urls = [];
                            $global_blacklists = 0;
                            $duplicates_across_team = 0;
                            $total_without_duplicate_blacklist = 0;
                            foreach ($csvData as $key => $value) {
                                $key = str_replace(['_', ' ', '-', ',', ';'], '', $key);
                                if (str_contains(strtolower($key), 'profileurl') || str_contains(strtolower($key), 'email')) {
                                    $has_url_or_email = true;
                                    foreach ($value as $url) {
                                        ++$count;
                                        if (in_array($url, $total_Urls)) {
                                            $duplicates++;
                                            $duplicate_Urls[] = $url;
                                        }
                                        if (filter_var($url, FILTER_VALIDATE_URL)) {
                                            if (stripos($url, 'https://www.linkedin.com/in/') !== false || stripos($url, 'https://www.linkedin.com/company/') !== false) {
                                                $total_Urls[] = $url;
                                                ++$total;
                                            } else {
                                                fclose($fileHandle);
                                                Storage::delete($uploadFilePath);
                                                return response()->json(['success' => false, 'message' => "Incorrect Linkedin Url Found at row " . $count, 'url' => $url]);
                                            }
                                        } else {
                                            fclose($fileHandle);
                                            Storage::delete($uploadFilePath);
                                            return response()->json(['success' => false, 'message' => "No URL Found at row " . $count]);
                                        }
                                    }
                                }
                            }
                            if (!$has_url_or_email) {
                                fclose($fileHandle);
                                Storage::delete($uploadFilePath);
                                return response()->json(['success' => false, 'message' => 'No Profile Url or Email Column Found']);
                            }
                            fclose($fileHandle);
                            $imported_leads = ImportedLeads::where('user_id', $user_id)->get();
                            if ($imported_leads->isEmpty()) {
                                $lead = new ImportedLeads();
                                $lead->user_id = $user_id;
                                $lead->file_path = $fileName;
                                $lead->created_at = now();
                                $lead->updated_at = now();
                                $lead->save();
                            } else {
                                foreach ($imported_leads as $imported_lead) {
                                    $prevFilePath = 'uploads/' . $imported_lead->file_path;
                                    Storage::delete($prevFilePath);
                                }
                                ImportedLeads::where('user_id', $user_id)->delete();
                                $lead = new ImportedLeads();
                                $lead->user_id = $user_id;
                                $lead->file_path = $fileName;
                                $lead->created_at = now();
                                $lead->updated_at = now();
                                $lead->save();
                            }
                            return response()->json([
                                'success' => true,
                                'total' => $total,
                                'total_Urls' => $total_Urls,
                                'duplicates' => $duplicates,
                                'duplicate_Urls' => $duplicate_Urls,
                                'global_blacklists' => $global_blacklists,
                                'duplicates_across_team' => $duplicates_across_team,
                                'total_without_duplicate_blacklist' => $total_without_duplicate_blacklist
                            ]);
                        } else {
                            fclose($fileHandle);
                            Storage::delete($uploadFilePath);
                            return response()->json(['success' => false, 'message' => "No Data Found"]);
                        }
                    } else {
                        Storage::delete($uploadFilePath);
                        return response()->json(['success' => false, 'message' => "Error storing file"]);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => "Invalid file type. Please upload a CSV file."]);
                }
            } else {
                return response()->json(['success' => false, 'message' => "Error uploading file"]);
            }
        } else {
            return redirect(url('/'));
        }
    }
}
