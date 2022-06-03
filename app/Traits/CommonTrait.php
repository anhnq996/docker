<?php

namespace App\Traits;

use App\Enums\ProfileStatus;
use App\Enums\TimeType;
use App\Enums\UserStatus;
use App\Enums\VideoSizeUnit;
use Carbon\Carbon;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use function PHPUnit\Framework\directoryExists;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait CommonTrait
{
    /**
     *
     * @param $profileId
     * @param $arrStatus
     * @return bool
     */
    public function checkProfileBelongToUser($profileId, $arrStatus): bool
    {
        $profileIds = array_merge(
            [auth()->user()->info_id],
            $this->userProfile->getProfileId($arrStatus)->pluck('profile_id')?->toArray()
        );
        if (!in_array($profileId, $profileIds)) {
            return false;
        }

        return true;
    }

    /**
     * @param $index
     * @param $prefix
     * @param $minLength
     * @return string
     */
    public function generateCouponCode($index, $prefix, $minLength): string
    {
        return $prefix . str_pad($index, $minLength, '0', STR_PAD_LEFT);
    }

    /**
     * @param $request
     * @param bool $isProfile
     * @return array
     */
    protected function prepareDataQuery($request, bool $isProfile = false): array
    {
        $limit         = is_numeric($request->get('limit')) ? $request->get('limit') : 20;
        $keyword       = $request->get('keyword');
        $gender        = $request->get('gender');
        $birthdayFrom  = $request->get('birthday_from') ? $request->get('birthday_from') : '';
        $birthdayTo    = $request->get('birthday_to') ? $request->get('birthday_to') : '';
        $isPrimary     = $request->get('is_primary');
        $createdFrom   = $request->get('created_at_from') ? $request->get('created_at_from') : '';
        $createdTo     = $request->get('created_at_to') ? $request->get('created_at_to') : '';
        $provinceId    = $request->get('province_id') ? $request->get('province_id') : '';
        $districtId    = $request->get('district_id') ? $request->get('district_id') : '';
        $wardId        = $request->get('ward_id') ? $request->get('ward_id') : '';
        $isActive      = $request->get('is_active') ? $request->get('is_active') : null;
        $existsConnect = $request->get('is_connect') ? $request->get('is_connect') : null;
        $activeFrom    = $request->get('active_from') ? $request->get('active_from') : '';
        $activeTo      = $request->get('active_to') ? $request->get('active_to') : '';

        if ($isProfile) {
            $status = $request->get('status') ? explode(',', $request->get('status'))
                : [ProfileStatus::active()->value, ProfileStatus::inactive()->value];
        } else {
            $status = $request->get('status') ? explode(',', $request->get('status'))
                : [UserStatus::active()->value, UserStatus::inactive()->value, UserStatus::otpVerified()->value];
        }

        return [
            $limit,
            $status,
            $keyword,
            $gender,
            $birthdayFrom,
            $birthdayTo,
            $isPrimary,
            $createdFrom,
            $createdTo,
            $provinceId,
            $districtId,
            $wardId,
            $isActive,
            $existsConnect,
            $activeFrom,
            $activeTo,
        ];
    }

    /**
     * Chức năng chỉ sử dụng để upload ảnh public và avatar.
     * Không sử dụng trong trường hợp ảnh cá nhân, kết quả khám hay nhật ký.
     *
     * @param UploadedFile|null $image
     * @param string|null $folder
     * @return string|null
     */
    protected function uploadPhoto(UploadedFile $image = null, string $folder = null): ?string
    {
        if (!($image instanceof UploadedFile)) {
            return null;
        }

        $extension    = $this->getExtension($image->getMimeType());
        $fileName     = Str::slug($image->getClientOriginalName(), '_');
        $fileName     = Str::random(6) . '_' . basename(
                str_replace(' ', '', $fileName),
                '.' . $extension
            ) . '_' . time() . '.' . $extension;
        $publicPath   = '/' . ltrim($folder . '/' . date('Y\/m\/d'), '/');
        $folderUpload = storage_path('/app/public' . $publicPath);
        if (!file_exists($folderUpload)) {
            mkdir($folderUpload, 0777, true);
        }
        $image->move($folderUpload, $fileName);

        return $publicPath . '/' . $fileName;
    }

    /**
     * Function Upload Image
     *
     * @param UploadedFile|null $image
     * @param string|null $folder
     * @return array|null
     */
    protected function uploadImage(UploadedFile $image = null, string $folder = null): ?array
    {
        if (!($image instanceof UploadedFile)) {
            return null;
        }
        $extension = $this->getExtension($image->getMimeType());
        $fileName  = Str::slug(now()->timestamp . Str::uuid()->getHex(), '_') . '.' . $extension;
        $path      = $folder . '/' . $fileName;
        Storage::put($path, $image->getContent());

        return [
            'path'      => $path,
            'full_path' => Storage::url($path),
        ];
    }

    /**
     * @param UploadedFile|null $file
     * @param string|null $folder
     * @return array|null
     */
    protected function uploadVideo(UploadedFile $file = null, string $folder = null): ?array
    {
        if (!($file instanceof UploadedFile)) {
            return null;
        }
        $extension = $this->getExtension($file->getMimeType());
        $fileName  = Str::slug(now()->timestamp . Str::uuid()->getHex(), '_') . '.' . $extension;
        $path      = $folder . '/' . $fileName;
        Storage::put($path, $file->getContent());

        $size = $file->getSize() / 1024;

        return array_merge(
            [
                'path'      => $path,
                'full_path' => Storage::url($path),
            ],
            $this->fileSizeFormatted($size)
        );
    }

    /**
     * @param $size
     * @return array|null
     */
    protected function fileSizeFormatted($size): ?array
    {
        $units = VideoSizeUnit::toValues();
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return [
            'size' => $size / pow(1024, $power),
            'unit' => $units[$power],
        ];
    }

    /**
     * @param $path
     * @param string $folder
     * @return string|null
     */
    protected function getThumbnail($path, $folder = 'video_thumbnail'): ?string
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
        ]);
        $video  = $ffmpeg->open($path);
        $video
            ->filters()
            ->resize(new Dimension(320, 240))
            ->synchronize();

        // Generate File Name
        $fileName     = now()->timestamp . Str::uuid()->getHex() . '.jpg';
        $publicPath   = '/' . ltrim($folder . '/' . date('Y\/m\/d'), '/');
        $folderUpload = storage_path('/app/public' . $publicPath);
        if (!file_exists($folderUpload)) {
            mkdir($folderUpload, 0777, true);
        }
        $exportPath = $folderUpload . '/' . $fileName;

        // Resize Video
        $video
            ->filters()
            ->resize(new Dimension(300, 300))
            ->synchronize();

        // Get thumbnail from frame
        $video
            ->frame(TimeCode::fromSeconds(5))
            ->save($exportPath);
        return $publicPath . '/' . $fileName;
    }

    /**
     * Function Upload File
     *
     * @param UploadedFile|null $file
     * @param string|null $folder
     * @return array|null
     */
    protected function uploadFile(UploadedFile $file = null, string $folder = null): ?array
    {
        if (!($file instanceof UploadedFile)) {
            return null;
        }
        $size      = round($file->getSize() / (1024 ** 3), 10);
        $extension = $this->getExtension($file->getMimeType());
        $fileName  = Str::slug(now()->timestamp . Str::uuid()->getHex(), '_') . '.' . $extension;
        $path      = $folder . '/' . $fileName;
        Storage::put($path, $file->getContent());

        return [
            'path'      => $path,
            'full_path' => Storage::url($path),
            'storage'   => $size,
            'name'      => $fileName,
        ];
    }

    /**
     * Paginate itens from collection
     *
     * @param array|Collection $items
     * @param int $perPage
     * @param int $page
     * @param array $options
     *
     * @return array
     */
    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: 1;

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $paginate = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return [
            'items' => $paginate->items(),
            'meta'  => [
                'total'        => $paginate->total(),
                'current_page' => $paginate->currentPage(),
                'last_page'    => $paginate->lastPage(),
                'per_page'     => $paginate->perPage(),
                'from'         => $paginate->firstItem(),
                'to'           => $paginate->lastItem(),
            ],
        ];
    }

    /**
     * @return string
     */
    public function generateRandomUniqueString(): string
    {
        $pattern = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        return substr(str_shuffle($pattern),
            0, 8);
    }

    /**
     * @param $fromTime
     * @param $toTime
     * @param $timeType
     * @return array
     */
    private function getDateFromTime($fromTime, $toTime, $timeType): array
    {
        $now = Carbon::now();
        switch ($timeType) {
            case TimeType::day()->value:
                $fromTime = empty($fromTime) ? $now->copy()->startOfDay() : Carbon::createFromFormat('Y-m-d', $fromTime)->startOfDay();
                $toTime   = empty($toTime) ? $now->copy()->endOfDay() : Carbon::createFromFormat('Y-m-d', $toTime)->endOfDay();
                break;
            case TimeType::month()->value:
                $fromTime = empty($fromTime) ? $now->copy()->startOfMonth() : Carbon::createFromFormat('Y-m-d', $fromTime . '-01')->startOfMonth();
                $toTime   = empty($toTime) ? $now->copy()->endOfMonth() : Carbon::createFromFormat('Y-m-d', $toTime . '-01')->endOfMonth();


                break;
            case TimeType::year()->value:
                $fromTime = empty($fromTime) ? $now->copy()->startOfYear() : Carbon::createFromFormat('Y-m-d', $fromTime . '-01-01')->startOfYear();
                $toTime   = empty($toTime) ? $now->copy()->endOfYear() : Carbon::createFromFormat('Y-m-d', $toTime . '-01-01')->endOfYear();
                break;
        }

        return [$fromTime, $toTime];
    }

    /**
     * @param $fromTime
     * @param $toTime
     * @param $timeType
     * @return array
     */
    private function getLastDateFromTime($fromTime, $timeType): array
    {
        $now    = Carbon::now();
        $toTime = null;

        switch ($timeType) {
            case TimeType::day()->value:
                $time     = empty($fromTime) ? $now : Carbon::createFromFormat('Y-m-d', $fromTime);
                $fromTime = empty($fromTime) ? $now->copy()->subDay()->startOfDay() : $time->copy()->subDay()->startOfDay();
                $toTime   = empty($fromTime) ? $now->copy()->subDay()->endOfDay() : $time->copy()->subDay()->endOfDay();
                break;
            case TimeType::month()->value:
                $time     = empty($fromTime) ? $now : Carbon::createFromFormat('Y-m-d', $fromTime . '-01');
                $fromTime = empty($fromTime) ? $now->copy()->subMonth()->startOfMonth() : $time->copy()->subMonth()->startOfMonth();
                $toTime   = empty($fromTime) ? $now->copy()->subMonth()->endOfMonth() : $time->copy()->subMonth()->endOfMonth();
                break;
            case TimeType::year()->value:
                $time     = empty($fromTime) ? $now : Carbon::createFromFormat('Y-m-d', $fromTime . '-01-01');
                $fromTime = empty($fromTime) ? $now->copy()->subYear()->startOfYear() : $time->copy()->subYear()->startOfYear();
                $toTime   = empty($fromTime) ? $now->copy()->subYear()->endOfYear() : $time->copy()->subYear()->endOfYear();
                break;
        }

        return [$fromTime, $toTime];
    }

    private function getExtension($mimeType)
    {
        $extensions = [
            'image/jpg'             => 'jpg',
            'image/jpeg'            => 'jpeg',
            'image/png'             => 'png',
            'image/bmp'             => 'bmp',
            'image/gif'             => 'gif',
            'image/svg'             => 'svg',
            'image/webp'            => 'webp',
            'video/x-flv'           => 'flv',
            'video/mp4'             => 'mp4',
            'application/x-mpegURL' => 'm3u8',
            'video/MP2T'            => 'ts',
            'video/3gpp'            => '3gp',
            'video/x-msvideo'       => 'avi',
            'video/x-ms-wmv'        => 'wmv',
        ];

        return $extensions[$mimeType] ?? 'png';
    }

    /**
     * @param $currentValue
     * @param $beforeValue
     * @return string
     */
    public function rate($currentValue, $beforeValue): string
    {
        if (((int)$currentValue == 0 && (int)$beforeValue == 0) || (int)$currentValue == 0) {
            $rate = 0 . '%';
        } elseif ((int)$beforeValue == 0) {
            $rate = 100 . '%';
        } else {
            $rate = number_format(round((int)$currentValue / (int)$beforeValue * 100, 0)) . '%';
        }

        return $rate;
    }
}
