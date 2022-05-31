<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlestarController extends Controller
{
    public function getStockBlestar()
    {
        // FTP server details
        $ftpHost   = 'ftp.blestar.net';
        $ftpUsername = 'blestarEx_PSupp@blestar.net';
        $ftpPassword = 'F6vqY3bqyj';

        // open an FTP connection
        $connId = ftp_connect($ftpHost) or die("Couldn't connect to $ftpHost");

        // try to login
        if (@ftp_login($connId, $ftpUsername, $ftpPassword)) {
            echo "Connected as $ftpUsername@$ftpHost";
        } else {
            echo "Couldn't connect as $ftpUsername";
        }

        // close the connection
        ftp_close($connId);
    }
}
