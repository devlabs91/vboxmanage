<?php 

namespace Devlabs91\Vboxmanage\Services;

use Devlabs91\Vboxmanage\Models\Vms;
use Devlabs91\Vboxmanage\Models\Vm;
use Devlabs91\Vboxmanage\Models\Snapshots;
use Devlabs91\Vboxmanage\Models\Snapshot;

class VmService {
    
    /** @var string */
    private $cmdVboxmanage;
    
    /** @var Vms */
    private $vms;
    
    public function __construct( ) {
        $this->whichVboxmanageCommand();
        $this->vms = $this->getListVms();
    }
    
    public function hasVm( $name ) {
        if( $this->vms->hasVmByKey( $name ) ) { return true; }
        return false;
    }
    
    public function getVm( $name ) {
        if( $this->hasVm( $name ) ) { return $this->vms->getVmByKey( $name ); }
        return null;
    }
    
    public function hasSnapshot( Vm $vm, $name ) {
        if ( $vm->getSnapshots()->hasSnapshotByKey( $name ) ) { return true; }
        return false;
    }
    
    public function getSnapshot( Vm $vm, $name ) {
        if( $this->hasSnapshot($vm, $name) ) { return $vm->getSnapshots()->getSnapshotByKey( $name ); }
        return null;
    }
    
    public function takeSnapshot(  Vm $vm, $name ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' snapshot '.$vm->getUuid().' take \''.$name.'\'';
        echo($cmd.PHP_EOL);
        exec( $cmd, $output);
        $this->vms = $this->getListVms();
    }
    
    /**
     * @return Vms
     */
    public function getListVms() {
        $vms = new Vms();
        $output = null;$cmd = $this->cmdVboxmanage . ' list vms';
        exec( $cmd, $output );
        if($output && is_array( $output) && sizeof($output)>0) {
            foreach($output AS $row) {
                $data = null;
                if(preg_match( '/^\"(.*)\" \{(.*)\}$/', $row, $data )) {
                    $vm = new Vm( $data[1], $data[2] );$this->loadSnapshots( $vm );
                    $vms->addVmByKey( $data[1], $vm );
                }
            }
        }
        return $vms;
    }
    
    private function loadSnapshots( Vm $vm ) {
        $vm->setSnapshots( new Snapshots() );
        $output = null;$cmd = $this->cmdVboxmanage . ' snapshot '.$vm->getUuid().' list';
        exec( $cmd, $output);
        if($output && is_array( $output) && sizeof($output)>0) {
            foreach($output AS $row) {
                $data = null;
                if(preg_match( '/.*Name\:\ (.*)\ \(UUID\:\ (.*)\).*/', $row, $data )) {
                    $vm->getSnapshots()->addSnapshotByKey($data[1], new Snapshot( $data[1],  $data[2]) );
                }
            }
        }
    }
    
    public function startVm( Vm $vm, $start ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' startvm '.$vm->getUuid().' --type '.$start;
        echo($cmd.PHP_EOL);
        exec( $cmd, $output);
    }
    
    public function stopVm( Vm $vm ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' controlvm '.$vm->getUuid().' poweroff ';
        echo($cmd.PHP_EOL);
        exec( $cmd, $output);
    }
    
    public function removeVm( Vm $vm ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' unregistervm '.$vm->getUuid().' --delete';
        echo($cmd.PHP_EOL);
        exec( $cmd, $output);
        $this->vms = $this->getListVms();
    }
    
    public function cloneVm( Vm $vm, Snapshot $snapshot, $cloneName ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' clonevm '.$vm->getUuid().' --snapshot '.$snapshot->getUuid().' --options link --name '.$cloneName.' --register';
        echo($cmd.PHP_EOL);
        exec( $cmd, $output);
        $this->vms = $this->getListVms();
    }
    
    public function removeNetwork( $network ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' dhcpserver remove --ifname '.$network['name'];
        exec( $cmd, $output);
        $output = null;$cmd = $this->cmdVboxmanage . ' hostonlyif remove '.$network['name'];
        exec( $cmd, $output);
    }
    
    public function createNetwork( $network ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' hostonlyif create';
        exec( $cmd, $output);
        $output = null;$cmd = $this->cmdVboxmanage . ' hostonlyif ipconfig '.$network['name'].' --ip '.$network['ip'].' --netmask 255.255.255.0';
        exec( $cmd, $output);
        $output = null;$cmd = $this->cmdVboxmanage . ' dhcpserver add --ifname '.$network['name'].' --ip '.$network['dhcp']['ip'].' --netmask 255.255.255.0 --lowerip '.$network['dhcp']['lowerip'].' --upperip '.$network['dhcp']['upperip'].' --enable';
        exec( $cmd, $output);
    }
    
    public function configHostonlyVm( Vm $vm, $nic, $name ) {
        $output = null;$cmd = $this->cmdVboxmanage . ' modifyvm '.$vm->getUuid().' --'.$nic.' hostonly --hostonlyadapter1 '.$name;
        echo($cmd.PHP_EOL);
        exec( $cmd, $output);
    }
    
    public function whichVboxmanageCommand() {
        $this->cmdVboxmanage = exec('which vboxmanage');
        if(!$this->cmdVboxmanage) { throw new \Exception( 'Vboxmanage not found', 404 ); }
    }
    
}