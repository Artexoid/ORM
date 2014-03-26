<?php

namespace PHPixie\ORM\Model;
include(__DIR__.'/Data/SubdocumentArray.php');
class Data {
    protected $data;
    protected $target;
    protected $propertiesSet = false;
    
    public function __construct($data, $target = null)
    {
        $this->data = $data;
        $this->target = $target !== null ? $target : $this;
        $this->setDataProperties();
    }
    
    public function setDataProperties()
    {
        foreach($this->data as $key => $value) {
            $this->target->$key = $this->normalizeValue($value);
        }
    }
    
    protected function normalizeValue($value)
    {
        if ($value instanceof \stdClass)
            $value = new \PHPixie\ORM\Model\Data($value);
        
        if (is_array($value))
            $value = new \PHPixie\ORM\Model\Data\SubdocumentArray($value);
        
        return $value;
    }
    
    public function currentProperties() {
        $currentData = get_object_vars($this->target);
        $classProperties = array_keys(get_class_vars(get_class($this->target)));
        foreach($classProperties as $property)
            unset($currentData[$property]);
		return $currentData;
    }
    
    public function modified()
    {
        $oldData = get_object_vars($this->data);
        $currentProperties = $this->currentProperties();
		$unset = array_diff(array_keys($oldData), array_keys($newData));
		$set = array();
		
		foreach($currentProperties as $key => $value) {
            if (!array_key_exists($key, $oldData)){
                $set[$key] = $this->denormalizeValue($value);
                continue;
            }
            
			if($oldValue instanceof \stdClass){
				if ($value instanceof static) {
					list($subSet, $subUnset) = $value->modified();
					
				}else
					$set[$key] = $this->denormalizeValue($value);
			}elseif(is_array($oldValue)) {
				if ($value instanceof \PHPixie\ORM\Model\Data\SubdocumentArray) {
					if ($value->isModified())
						$set[$key] = $this->denormalizeValue($value);
				}else
					$set[$key] = $this->denormalizeValue($value);
			}elseif($oldValue != $value)
				$set[$key] = $this->denormalizeValue($value);
        }
        
        return array($set, $unset);
    }
    
}

$old = new \stdClass;
$old->a = 5;
$old->b = 'pixie';
$old->c = new \stdClass;
$old->d = new \stdClass;
$old->d->da = new \stdClass;
$old->e = new \stdClass;

$old->c->ca = 'trixie';
$old->c->cb = array(5, 6);

$old->d->da->daa = 4;
$old->d->da->dab = 'test';
$old->d->da->dac = 6;

$old->d->db = 3;

$old->e->ea = 5;

$d = new Data($old);

unset($d->a);
$d->b = 'trixie';
$d->c->ca = 'pixie';
array_push($d->c->cb, 2);

$d->d->dc = new \stdClass;
$d->d->dc->dca = 5;
unset($d->d->da->daa);
$d->d->da->dab = 4;
    
$d->e = 8;
$d->f = 9;




print_r($d->modified());