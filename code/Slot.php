<?php
class Slot extends DataObject {
	
	
	static $db = array(
		"Name" => "Varchar"
	);
	
	static $has_many = array(
		"Elements" => "Element"
	);
	
	static $has_one = array(
		"GridPage" => "GridPage"
	);
	
	
	function forTemplate() {
		$Content = "";
		if($this->Elements() && $Elements = $this->Elements()){
			foreach($Elements as $Element) {
				
				$ExtraAttrs = array();
				
				$ID = $Element->HTMLID();
				
				$Classes = array(
					$Element->parentClass(),
					$Element->ClassName
				 );
				
				(!empty($Element->ExtraClass)?$Classes[] = $Element->ExtraClass:"");
				
				if(!empty($Element->ExtraStyles) && $ExtraStyles = $Element->ExtraStyles) {
					$ExtraAttrs[] = "style=\"{$ExtraStyles}\"";
				}
				
				$ClassStr = implode(" ", $Classes);
				$ExtraStr = implode(" ", $ExtraAttrs);
				
				$Content .=<<<HTML
				
<div class="{$ClassStr}" {$ExtraStr} id="{$ID}">
	{$Element->Prefix}
	{$Element->forTemplate()}
	{$Element->Suffix}
</div>
		
HTML;
			}
		}
		return $Content;
	}
	
	
	function forCMSTemplate() {
		
		$Content = "";
		if($this->Elements()){
			foreach($this->Elements() as $Element) {
$Content .=<<<HTML
<div class="{$Element->parentClass()} {$Element->ClassName} {$Element->HTMLID()}" id="{$Element->parentClass()}-{$Element->ID}">
	{$Element->Prefix}
	{$Element->forCMSTemplate()}
	{$Element->Suffix}
</div>
HTML;
			}
		}
		return $Content;
	}
	
	
	function getCMSFields_forPopup() {
		$fs = new FieldSet(
			new ElementManager(
				$this,
				"Elements",
				"Element",
				array(
					'Name' => 'Name',
					'ClassNiceName' => 'Type',
					'Content' => 'Content'
				),
				"CMSFields",
				"SlotID='{$this->ID}'"
				)
			);
		return $fs;
	}
	
	
	function onBeforeDelete() {
		if($this->Elements() && $Elements = $this->Elements()) {
			foreach($Elements as $Element) {
				$Element->delete();
			}
		}
		parent::onBeforeDelete();
	}
	
	
	function AddLink() {
		return "admin/EditForm/field/Slots/item/{$this->ID}/DetailForm/field/Elements/add/";
	}
	
}
