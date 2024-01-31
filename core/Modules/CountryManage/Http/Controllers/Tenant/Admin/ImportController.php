<?php

namespace Modules\CountryManage\Http\Controllers\Tenant\Admin;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;

class ImportController extends Controller
{
    public function import_settings()
    {
        return view('countrymanage::tenant.admin.import-settings');
    }

    public function update_import_settings(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:150000'
        ]);


        if ($request->hasFile('csv_file')) {
            $file = $request->csv_file;
            $extension = $file->getClientOriginalExtension();
            if ($extension == 'csv') {
                //copy file to temp folder

                $old_file = Session::get('import_csv_file_name');
                if (file_exists('assets/uploads/import/' . $old_file)) {
                    @unlink('assets/uploads/import/' . $old_file);
                }
                $file_name_with_ext = $file->getClientOriginalName();

                $file_name = pathinfo($file_name_with_ext, PATHINFO_FILENAME);
                $file_name = strtolower(Str::slug($file_name));

                $file_tmp_name = $file_name . time() . '.' . $extension;
                $file->move('assets/uploads/import', $file_tmp_name);

                $data = array_map('str_getcsv', file('assets/uploads/import/' . $file_tmp_name));
                $csv_data = $this->formatCsvData($data);
                if (!is_array($csv_data) && $csv_data->getStatusCode() == 302)
                {
                    @unlink('assets/uploads/import/' . $file_tmp_name);
                    return back();
                }

                Session::put('import_csv_file_name', $file_tmp_name);
                return view('countrymanage::tenant.admin.import-settings', [
                    'import_data' => $csv_data,
                ]);
            }

        }

        return back();
    }

    public function cancel_import_settings()
    {
        $old_file = Session::get('import_csv_file_name');
        if (file_exists('assets/uploads/import/' . $old_file)) {
            @unlink('assets/uploads/import/' . $old_file);
            Session::forget('import_csv_file_name');
        }

        return back();
    }

    public function import_to_database_settings(Request $request)
    {
        $file_tmp_name = Session::get('import_csv_file_name');
        $data = array_map('str_getcsv', file('assets/uploads/import/' . $file_tmp_name));
        $csv_data = $this->formatCsvData($data);

        $return_info = [];
        if (is_array($csv_data))
        {
            if (array_key_exists('country', $csv_data) && array_key_exists('state', $csv_data) && array_key_exists('city', $csv_data))
            {
                $countries = $csv_data['country'];
                $states = $csv_data['state'];
                $cities = $csv_data['city'];

                try {
                    if (!empty($countries))
                    {
                        Country::InsertOrIgnore($countries);
                    }

                    if (!empty($states))
                    {
                        State::InsertOrIgnore($states);
                    }

                    if (!empty($cities))
                    {
                        City::InsertOrIgnore($cities);
                    }

                    $return_info = [
                        'type' => 'success',
                        'msg' => __('Country, state and city imported successfully')
                    ];
                } catch (\Exception $exception)
                {
                    $return_info = [
                        'type' => 'danger',
                        'msg' => __('Import failed.')
                    ];
                }
            } else {
                $return_info = [
                    'type' => 'danger',
                    'msg' => __('Import failed. The file format is incorrect')
                ];
            }
        }

        if (!empty($return_info))
        {
            $this->deleteFile();
        }
        return back()->with(FlashMsg::explain($return_info['type'] ?? '', $return_info['msg'] ?? ''));
    }

    private function deleteFile()
    {
        $file_tmp_name = Session::get('import_csv_file_name');
        @unlink('assets/uploads/import/' . $file_tmp_name);
        return true;
    }

    private function formatCsvData($data)
    {
        $finalData = [];
        if (!empty($data))
        {
            foreach ($data as $item)
            {
                if(count($item) !== 4)
                {
                    return back()->with(FlashMsg::explain('danger', __('CSV format is incorrect')));
                }

                if (empty($item[2]) && empty($item[3]))
                {
                    if (!empty(current($item)) && current($item) != 'COUNTRY_ID')
                    {
                        $finalData['country'][current($item)] = [
                            'id' => current($item),
                            'name' => $item[1]
                        ];
                    }
                }

                if (!empty($item[2]) && empty($item[3]))
                {
                    if (!empty(current($item)) && current($item) != 'STATE_ID')
                    {
                        $finalData['state'][current($item)] = [
                            'id' => current($item),
                            'country_id' => $item[1],
                            'name' => $item[2]
                        ];
                    }
                }

                if (!empty($item[2]) && !empty($item[3]))
                {
                    if (!empty(current($item)) && current($item) != 'CITY_ID')
                    {
                        $finalData['city'][current($item)] = [
                            'id' => current($item),
                            'state_id' => $item[1],
                            'country_id' => $item[2],
                            'name' => $item[3]
                        ];
                    }
                }
            }
        }

        return $finalData;
    }

    public function sample_download()
    {
        return response()->file("core/Modules/CountryManage/sample-data.csv");
    }
}
