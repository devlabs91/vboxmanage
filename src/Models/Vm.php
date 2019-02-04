<?php 

namespace Devlabs91\Vboxmanage\Models;

class Vm {

    private $name;
    private $uuid;
    
    /** @var Snapshots */
    private $snapshots;
    
    public function __construct( $name = null, $uuid = null ) {
        $this->name = $name;
        $this->uuid = $uuid;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getUuid() {
        return $this->uuid;
    }
    
    public function getSnapshots() {
        return $this->snapshots;
    }
    
    public function setSnapshots( Snapshots $snapshots ) {
        $this->snapshots = $snapshots;
    }
    
}
