<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\HandleImageUploadService;
use App\Models\MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Storage;
use function PHPUnit\Framework\isNull;

class MediaUploaderController extends Controller
{
    private function folderPrefix(){
        return is_null(tenant()) ? 'landlord' : 'tenant';
    }

    public function upload_media_file(Request $request)
    {
//        $this->validate($request,[
//            'file' => 'nullable|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,svg,zip,csv,xlsx,xlsm,xlsb,xltx,pptx,pptm,ppt|max:10240'
//        ]);

        if (tenant())
        {
            $storage_limit = tenant()?->payment_log?->package?->storage_permission_feature * 1024; // MB to KB
            $directory_size = $this->getTenantDirectorySize() / 1024; // Byte to KB

            if (tenant()?->payment_log?->package?->storage_permission_feature != -1 && $directory_size >= $storage_limit)
            {
                return \Response::make(array('file' => false, 'error' => __('Your storage limit is over! You can not upload more media files')), 400);
            }
        }

        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished())
        {
            $this->insert_media_image($save->getFile(), $request->user_type, $request);
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
        }


//        if ($request->hasFile('file')) {
//
//            $image = $request->file;
//
//            $image_extenstion = $image->extension();
//            $image_name_with_ext = $image->getClientOriginalName();
//
//            $image_name = pathinfo($image_name_with_ext, PATHINFO_FILENAME);
//            $image_name = strtolower(Str::slug($image_name));
//
//            $image_db = $image_name . time() . '.' . $image_extenstion;
//
//            //TODO:: write method to handle file upload
//            $tenant_path = '';
//            if(tenant()){
//                $tenant_user = tenant() ?? null;
//                $tenant_path = !is_null($tenant_user) ? $tenant_user->id.'/' : '';
//            }
//            $folder_path = global_assets_path('assets/'. $this->folderPrefix().'/uploads/media-uploader/'.$tenant_path);
//
//            if (in_array($image_extenstion,['pdf','doc','docx','txt','svg','zip','csv','xlsx','xlsm','xlsb','xltx','pptx','pptm','ppt'])){
//                $upload_folder = '/';
//                if (in_array(Storage::getDefaultDriver(),['s3','cloudFlareR2'])){
//                    $upload_folder = is_null(tenant()) ? '/' : tenant()->getTenantKey().'/';
//                }
//
//                Storage::putFileAs($upload_folder, $image, $image_db);
//                $storage_driver = Storage::getDefaultDriver();
//
//                $imageData = [
//                    'title' => $image_name_with_ext,
//                    'size' => null,
//                    'user_type' => 0, //0 == admin 1 == user
//                    'path' => $image_db,
//                    'dimensions' => null,
//                    'user_id' =>  \Auth::guard('admin')->id(),
//                    'load_from' => in_array($storage_driver,['TenantMediaUploader','LandlordMediaUploader']) ? 0 : 1,
//                ];
//
//                if ($request->user_type === 'user'){
//                    $imageData['user_type'] = 1;
//                    $imageData['user_id'] = \Auth::guard('web')->id();
//                }
//
//                MediaUploader::create($imageData);
//            }
//
//            if (in_array($image_extenstion,['jpg','jpeg','png','gif','webp'])){
//                HandleImageUploadService::handle_image_upload(
//                    $image_db,
//                    $image,
//                    $image_name_with_ext,
//                    $folder_path,
//                    $request
//                );
//            }
//        }
    }

    public function insert_media_image($file, $userType, $request) {
        $image = $file;
        $image_extenstion = $image->extension();
        $image_name_with_ext = $image->getClientOriginalName();

        $image_name = pathinfo($image_name_with_ext, PATHINFO_FILENAME);
        $image_name = strtolower(Str::slug($image_name));

        $image_db = $image_name . time() . '.' . $image_extenstion;

        //TODO:: write method to handle file upload
        $tenant_path = '';
        if(tenant()){
            $tenant_user = tenant() ?? null;
            $tenant_path = !is_null($tenant_user) ? $tenant_user->id.'/' : '';
        }
        $folder_path = global_assets_path('assets/'. $this->folderPrefix().'/uploads/media-uploader/'.$tenant_path);

        if (in_array($image_extenstion, ['pdf','doc','docx','txt','svg','zip','csv','xlsx','xlsm','xlsb','xltx','pptx','pptm','ppt','mp4','avi','flv'])){

            $upload_folder = '/';
            if (cloudStorageExist() && in_array(Storage::getDefaultDriver(),['s3','cloudFlareR2','wasabi'])){
                $upload_folder = is_null(tenant()) ? '/' : tenant()->getTenantKey().'/';
            }

            Storage::putFileAs($upload_folder, $image, $image_db); //$request->file->move($folder_path, $image_db);
            $storage_driver = Storage::getDefaultDriver();

            $imageData = [
                'title' => $image_name_with_ext,
                'size' => null,
                'user_type' => 0, //0 == admin 1 == user
                'path' => $image_db,
                'dimensions' => null,
                'user_id' =>  \Auth::guard('admin')->id(),
                'load_from' => in_array($storage_driver,['TenantMediaUploader','LandlordMediaUploader']) ? 0 : 1,
            ];

            if ($userType === 'user'){
                $imageData['user_type'] = 1;
                $imageData['user_id'] = \Auth::guard('web')->id();
            }

            MediaUploader::create($imageData);
        }

        if (in_array($image_extenstion, ['jpg','jpeg','png','gif','webp'])){
            HandleImageUploadService::handle_image_upload(
                $image_db,
                $image,
                $image_name_with_ext,
                $folder_path,
                $request
            );
        }
    }

    public function all_upload_media_file(Request $request)
    {
        $image_query = MediaUploader::query();
        if ($request->user_type === 'user'){
            $image_query->where(['user_type' => 1,'user_id' => \Auth::guard('web')->id()]);
        }

        $all_images = $image_query->orderBy('id', 'DESC')->take(20)->get();
        $selected_image = MediaUploader::find((int) $request->selected);
        $all_image_files = [];

//        if (!is_null($selected_image)){
//            if ($this->check_file_exists($selected_image->path)) {
//                $image_url = $this->getUploadAssetPath($selected_image->path);
//                if ($this->check_file_exists('grid/grid-'.optional($selected_image)->path)) {
//                    $image_url = $this->getUploadAssetPath('grid/grid-' . optional($selected_image)->path);
//                }
//                $all_image_files[] = [
//                    'image_id' => $selected_image->id,
//                    'title' => $selected_image->title,
//                    'dimensions' => $selected_image->dimensions,
//                    'alt' => $selected_image->alt,
//                    'size' => $selected_image->size,
//                    'type' => pathinfo($image_url,PATHINFO_EXTENSION),
//                    'user_type' => $selected_image->type === 0 ? 'admin' : 'user',
//                    'path' => $selected_image->path,
//                    'img_url' => $image_url,
//                    'upload_at' => date_format($selected_image->created_at, 'd M y')
//                ];
//            }else{
//                //todo:: delete assets as well
//                $this->deleteOldFile($selected_image->path);
//                MediaUploader::find($selected_image->id)->delete();
//            }
//        }

        foreach ($all_images as $image){
            if (!is_null($selected_image) && $image->id === $selected_image->id){
                continue;
            }

            if ($this->check_file_exists($image->path, load_from:$image->load_from)){
                $image_url = $this->getUploadAssetPath($image->path, load_from:$image->load_from);
                if ($this->check_file_exists('grid/grid-'.$image->path, load_from:$image->load_from)) {
                    $image_url = $this->getUploadAssetPath('grid/grid-' . $image->path, load_from:$image->load_from);
                }

                $all_image_files[] = [
                    'image_id' => $image->id,
                    'title' => $image->title,
                    'dimensions' => $image->dimensions,
                    'alt' => $image->alt,
                    'size' => $image->size,
                    'type' => pathinfo($image_url,PATHINFO_EXTENSION),
                    'path' => $image->path,
                    'user_type' => $image->type === 0 ? 'admin' : 'user',
                    'img_url' => $image_url,
                    'upload_at' => date_format($image->created_at, 'd M y')
                ];

            }else{
                //todo:: delete assets as well
                $this->deleteOldFile($image->path);
                MediaUploader::find($image->id)->delete();
            }
        }

        return response()->json($all_image_files);
    }

    public function delete_upload_media_file(Request $request)
    {
        $image_query = MediaUploader::query();
        if ($request->user_type === 'user'){
            $image_query->where(['user_type' => 1,'user_id' => \Auth::guard('web')->id()]);
        }
        $get_image_details = $image_query->where('id',$request->img_id)->first();
        $this->deleteOldFile($get_image_details->path);

        $get_image_details->delete();

        return redirect()->back();
    }

    public function alt_change_upload_media_file(Request $request){
        $this->validate($request,[
            'imgid' => 'required',
            'alt' => 'nullable',
        ]);
        $image_query = MediaUploader::query();
        if ($request->user_type === 'user'){
            $image_query->where(['user_type' => 1,'user_id' => \Auth::guard('web')->id()]);
        }
        $image_query->where('id',$request->imgid)->update(['alt' => $request->alt]);
        return 'alt update done';
    }


    public function get_image_for_load_more(Request $request){
        $image_query = MediaUploader::query();
        if ($request->user_type === 'user'){
            $image_query->where(['user_type' => 1,'user_id' => \Auth::guard('web')->id()]);
        }
        $all_images = $image_query->orderBy('id', 'DESC')->skip($request->skip)->take(20)->get();
        if ( $all_images->count() < 20){
            return response()->json([]);
        }
        $all_image_files = [];
        foreach ($all_images as $image){
            if ($this->check_file_exists($image->path, load_from:$image->load_from))
            {
                $image_url = $this->getUploadAssetPath($image->path, load_from:$image->load_from);
                if ($this->check_file_exists('grid-'.$image->path)) {
                    $image_url = $this->getUploadAssetPath('/grid-' . $image->path, load_from:$image->load_from);
                }

                $all_image_files[] = [
                    'image_id' => $image->id,
                    'title' => $image->title,
                    'dimensions' => $image->dimensions,
                    'alt' => $image->alt,
                    'type' => pathinfo($image_url,PATHINFO_EXTENSION),
                    'user_type' => $image->type === 0 ? 'admin' : 'user',
                    'size' => $image->size,
                    'path' => $image->path,
                    'img_url' => $image_url,
                    'upload_at' => date_format($image->created_at, 'd M y')
                ];

            }
        }

        return response()->json($all_image_files);
    }

//    private function handle_image_upload(
//        $image_db,
//        $image,
//        $image_name_with_ext,
//        $folder_path,
//        $request
//    ){
//        $image_dimension = getimagesize($image);
//        $image_width = $image_dimension[0];
//        $image_height = $image_dimension[1];
//        $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
//        $image_size_for_db = $image->getSize();
//
//        $image_grid = 'grid-'.$image_db ;
//        $image_large = 'large-'. $image_db;
//        $image_thumb = 'thumb-'. $image_db;
//        $image_tiny = 'tiny-'. $image_db;
//
//        $resize_grid_image = Image::make($image)->resize(350, null,function ($constraint) {
//            $constraint->aspectRatio();
//        });
//        $resize_large_image = Image::make($image)->resize(740, null,function ($constraint) {
//            $constraint->aspectRatio();
//        });
//        $resize_thumb_image = Image::make($image)->resize(150, 150);
//        $resize_tiny_image = Image::make($image)->resize(15, 15)->blur(50);
//        $request->file->move($folder_path, $image_db);
//
//        $imageData = [
//            'title' => $image_name_with_ext,
//            'size' => formatBytes($image_size_for_db),
//            'path' => $image_db,
//            'user_type' => 0, //0 == admin 1 == user
//            'user_id' => \Auth::guard('admin')->id(),
//            'dimensions' => $image_dimension_for_db
//        ];
//        if ($request->user_type === 'user'){
//            $imageData['user_type'] = 1;
//            $imageData['user_id'] = \Auth::guard('web')->id();
//        }
//        else if ($request->user_type === 'api'){
//            $imageData['user_type'] = 1;
//            $imageData['user_id'] = \Auth::guard('sanctum')->id();
//        }
//
//         MediaUploader::create($imageData);
//
//        if ($image_width > 150){
//            $resize_thumb_image->save($folder_path .'thumb/'. $image_thumb);
//            $resize_grid_image->save($folder_path .'grid/'. $image_grid);
//            $resize_large_image->save($folder_path .'large/'. $image_large);
//
//            $tiny_path = $folder_path .'tiny';
//            if (!is_dir($tiny_path))
//            {
//                mkdir($tiny_path, 0777);
//            }
//            $resize_tiny_image->save($folder_path .'tiny/'. $image_tiny);
//        }
//    }

    private function deleteOldFile($get_image_details) : void
    {
        $file_type_list = ['','grid/grid-','large/large-','thumb/thumb-','tiny/tiny-'];
        foreach ($file_type_list as $file_type){
//            if ($this->check_file_exists($file_type.$get_image_details)){
                @unlink($this->getUploadBasePath($file_type.$get_image_details));
//            }
        }
    }

    private function check_file_exists($path, $load_from = 0) : bool
    {
        $file_path = $this->getUploadBasePath($path);
        //todo:: change code here to make it work with mediaUploader and cloudflare R2
        try {
            $folder = is_null(tenant()) ? $path : tenant()->getTenantKey()."/";
            $driver = \Illuminate\Support\Facades\Storage::getDefaultDriver();
            if ($load_from == 0 && !is_null(tenant())){
                $driver = "TenantMediaUploader";
            }elseif($load_from == 0 && is_null(tenant())){
                $driver = "LandlordMediaUploader";
            }
            $upload_folder = '/';
            if (cloudStorageExist() && in_array(Storage::getDefaultDriver(),['s3','cloudFlareR2','wasabi'])  && $load_from === 1){
                $upload_folder = is_null(tenant()) ? '/' : tenant()->getTenantKey().'/';
            }

            return Storage::disk($driver)->fileExists($upload_folder.$path);
        }catch (\Exception $e){
            return "";
        }

//        $file_path = $this->getUploadBasePath($path);
//        return file_exists($file_path) && !is_dir($file_path);
    }

    private function getUploadBasePath($path = '', $load_from = 0) :string
    {
        return Storage::renderUrl($path, load_from:$load_from);
//        return global_assets_path('assets/'.$this->folderPrefix().'/uploads/media-uploader/'.$this->getTenantFolderPath().$path);
    }

    private function getUploadAssetPath($path = '', $load_from=0) :string
    {
        return Storage::renderUrl($path,load_from:$load_from);
//        return global_asset('assets/'.$this->folderPrefix().'/uploads/media-uploader/'.$this->getTenantFolderPath().$path);
    }

    private function getTenantFolderPath(){
        $tenant_user = '';
        if(tenant()){
            $tenant = tenant() ?? null;
            $tenant_user = !is_null($tenant) ? $tenant : '';
        }

        return !is_null($tenant_user) ? optional($tenant_user)->id.'/' : '';
    }

    private function getTenantDirectorySize()
    {
        $file_size = 0;
        $scan_path = Storage::disk("root_url")->allFiles('assets/'. $this->folderPrefix().'/uploads/media-uploader/'.tenant()->id);

        foreach( $scan_path as $file)
        {
            clearstatcache();
            $exploded = explode('/', $file);
            if ($exploded[count($exploded)-1] === '.DS_Store' || $file === 'NAN')
            {
                continue;
            }

            $file_size += filesize($file);
        }

        return $file_size;
    }
}
