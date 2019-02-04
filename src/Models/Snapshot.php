<?php 

namespace Devlabs91\Vboxmanage\Models;

class Snapshot {

    private $name;
    private $uuid;
    
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
    
}
