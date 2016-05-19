<?php
namespace composerScript;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        // do stuff
    }


    public static function postPackageInstall( PackageEvent $event )
    {
        $packageName = $event->getOperation()->getPackage()->getName();

        switch($packageName){
            case 'almasaeed2010/adminlte' : copyAdminLTEFiles();  break;
            case 'twbs/bootstrap'         : copyBootstrapFiles(); break;
        }

    }

    public static function postPackageUpdate( PackageEvent $event )
    {
        $packageName = $event->getOperation()->getInitialPackage()->getName();

        switch($packageName){
            case 'almasaeed2010/adminlte' : copyAdminLTEFiles();  break;
            case 'twbs/bootstrap'         : copyBootstrapFiles(); break;
        }
    }


    public static function warmCache(Event $event)
    {
        // make cache toasty
    }

    public static function copyAdminLTEFiles(){
        shell_exec('mkdir -p web/components/AdminLTE');
        shell_exec('rm -rf web/components/AdminLTE/*');
        shell_exec('cp -rf vendor/almasaeed2010/adminlte/dist/    web/components/AdminLTE/');
        shell_exec('cp -rf vendor/almasaeed2010/adminlte/plugins/ web/components/AdminLTE/');
    }

    public static function copyBootstrapFiles(){
        shell_exec('mkdir -p web/components/bootstrap');
        shell_exec('rm -rf web/components/bootstrap/*');
        shell_exec('cp -rf vendor/twbs/bootstrap/dist/* web/components/bootstrap/');
    }
}