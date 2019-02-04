<?php 

namespace Devlabs91\Vboxmanage\Models;

class Vms {

    /** @var Vm[] */
    private $vms;
    
    public function __construct( array $vms = [] ) {
        $this->vms = $vms;
    }
    
    public function addVmByKey( $key, Vm $vm ) {
        $this->vms[$key] = $vm;
    }
    
    public function getVmByKey( $key ) {
        if(array_key_exists($key, $this->vms)) {
            return $this->vms[$key];
        }
        return null;
    }
    
    public function hasVmByKey( $key ) {
        if(array_key_exists($key, $this->vms)) {
            return true;
        }
        return false;
    }
    
    public function setVms( array $vms ) {
        $this->vms = [];
    }
    
    public function getVms() {
        return $this->vms;
    }
    
}
