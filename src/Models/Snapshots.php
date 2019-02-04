<?php 

namespace Devlabs91\Vboxmanage\Models;

class Snapshots {

    private $snapshots;
    
    public function __construct( array $snapshots = [] ) {
        $this->snapshots = $snapshots;
    }
    
    public function addSnapshotByKey( $key, Snapshot $snapshot ) {
        $this->snapshots[$key] = $snapshot;
    }
    
    public function getSnapshotByKey( $key ) {
        if(array_key_exists($key, $this->snapshots)) {
            return $this->snapshots[$key];
        }
        return null;
    }
    
    public function hasSnapshotByKey( $key ) {
        if(array_key_exists($key, $this->snapshots)) {
            return true;
        }
        return false;
    }
    
    public function setSnapshots( array $snapshots ) {
        $this->snapshots = [];
    }
    
    public function getSnapshots() {
        return $this->snapshots;
    }
    
}
