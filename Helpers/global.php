<?php

if (!function_exists('is_image')) {

    /**
     * @param $path
     * @return bool
     */
    function is_image($path)
    {
        $images = [
            'jpg', 'jpeg', 'png', 'bmp', 'gif'
        ];

        $info = pathinfo($path);

        $extension = $info['extension'];

        return in_array($extension, $images);
    }
}

if (!function_exists('extension_image')) {

    /**
     * @param $path
     * @param string $size
     * @return string
     */
    function extension_image($path, $size = 'md')
    {
        $ignore = [
            'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg'
        ];

        $sizes = [
            'xs' => '16px',
            'sm' => '32px',
            'md' => '48px',
            'lg' => '512px',
        ];

        if (!isset($sizes[$size])) {
            throw \Exception('Size ' . $size . ' doesn\'t exist. Available sizes - xs, sm, md, lg');
        }

        $size = $sizes[$size];

        $info = pathinfo($path);

        $extension = $info['extension'];

        if (in_array($extension, $ignore)) {
            return asset($path);
        } else {
            return asset('assets/crud/images/icons/' . $size . '/' . $extension . '.png');
        }
    }
}

/**
 * These file functions come from here - https://stackoverflow.com/a/22500394/4066921
 */

if (!function_exists('getMaximumFileUploadSize')) {

    /**
     * This function returns the maximum files size that can be uploaded
     * in PHP
     * @returns int File size in bytes
     **/
    function getMaximumFileUploadSize()
    {
        return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
    }
}

if (!function_exists('convertPHPSizeToBytes')) {
    /**
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     *
     * @param string $sSize
     * @return integer The value in bytes
     */
    function convertPHPSizeToBytes($sSize)
    {
        $sSuffix = strtoupper(substr($sSize, -1));
        if (!in_array($sSuffix, array('P', 'T', 'G', 'M', 'K'))) {
            return (int)$sSize;
        }
        $iValue = substr($sSize, 0, -1);
        switch ($sSuffix) {
            case 'P':
                $iValue *= 1024;
            // Fallthrough intended
            case 'T':
                $iValue *= 1024;
            // Fallthrough intended
            case 'G':
                $iValue *= 1024;
            // Fallthrough intended
            case 'M':
                $iValue *= 1024;
            // Fallthrough intended
            case 'K':
                $iValue *= 1024;
                break;
        }

        return (int)$iValue;
    }
}