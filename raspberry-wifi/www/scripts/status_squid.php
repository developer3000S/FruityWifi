<? 
/*
	Copyright (C) 2013  xtr4nge [_AT_] gmail.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../login_check.php";
include "../config/config.php";
include "../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["file"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($iface_wifi, "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];

if($service == "squid") {
    if ($action == "start") {
        $exec = "/usr/sbin/squid3 -f /raspberry-wifi/conf/squid.conf &";
        exec("../bin/danger \"" . $exec . "\"" );
        $exec = "/sbin/iptables -t nat -A PREROUTING -i $iface_wifi -p tcp --dport 80 -j REDIRECT --to-port 3128";
        exec("../bin/danger \"" . $exec . "\"" );
    } else if($action == "stop") {
        $exec = "/usr/bin/killall squid3";
        exec("../bin/danger \"" . $exec . "\"" );
        $exec = "/etc/init.d/squid3 stop";
        exec("../bin/danger \"" . $exec . "\"" );
        $exec = "/sbin/iptables -t nat -D PREROUTING -i $iface_wifi -p tcp --dport 80 -j REDIRECT --to-port 3128";
        exec("../bin/danger \"" . $exec . "\"" );
    }
}

if($service == "url_rewrite") {
    if ($action == "start") {
        $exec = "/bin/sed -i 's/^\#url_rewrite_program/url_rewrite_program/g' /raspberry-wifi/conf/squid.conf";
        exec("../bin/danger \"" . $exec . "\"" );
        $exec = "/usr/sbin/squid3 -k reconfigure";
        exec("../bin/danger \"" . $exec . "\"" );
    } else if($action == "stop") {
        $exec = "/bin/sed -i 's/^url_rewrite_program/\#url_rewrite_program/g' /raspberry-wifi/conf/squid.conf";
        exec("../bin/danger \"" . $exec . "\"" );
        $exec = "/usr/sbin/squid3 -k reconfigure";
        exec("../bin/danger \"" . $exec . "\"" );
    }
        header('Location: ../page_squid.php');
        exit;
}

if($service == "iptables") {
    if ($action == "start") {
        $exec = "/sbin/iptables -t nat -A PREROUTING -i $iface_wifi -p tcp --dport 80 -j REDIRECT --to-port 3128";
        exec("../bin/danger \"" . $exec . "\"" );
    } else if($action == "stop") {
        $exec = "/sbin/iptables -t nat -D PREROUTING -i $iface_wifi -p tcp --dport 80 -j REDIRECT --to-port 3128";
        exec("../bin/danger \"" . $exec . "\"" );
    }
    header('Location: ../page_squid.php');
    exit;
}

if($service == "js") {
    $exec = "/bin/sed -i 's/url_rewrite_program=.*/url_rewrite_program=\\\"".$action.".js\\\";/g' ../config/config.php";
    exec("../bin/danger \"" . $exec . "\"" );
    $exec = "/bin/cp /raspberry-wifi/squid.inject/$action.js /raspberry-wifi/squid.inject/pasarela.js";
    exec("../bin/danger \"" . $exec . "\"" );
}

if ($page == "module") {
    header('Location: ../page_squid.php');
} else {
    header('Location: ../action.php');
}


?>