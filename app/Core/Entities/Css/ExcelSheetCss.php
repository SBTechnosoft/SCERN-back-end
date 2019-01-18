<?php
namespace ERP\Core\Entities\Css;

use stdclass;
use PHPExcel_Style_Alignment;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExcelSheetCss
{
	public function getCss()
	{
		// style for header
		$headerStyleArray = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => '#00000'),
			'size'  => 12,
			'name'  => 'Calibri'
		));
		
		// style for Title
		$titleStyleArray = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => '#00000'),
			'size'  => 14,
			'name'  => 'Calibri'
		));
		
		//style for text in center alignment
		$alignmentCenter = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);
		
		//style for text in right alignment
		$alignmentRight = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			)
		);
		
		// style for Bold
		$boldResultArray = array(
		'font'  => array(
			'bold'  => true,
			'size'  => 11,
			'color' => array('rgb' => '#00000'),
			'name'  => 'Calibri'
		));
		
		$cssObjectClass = new stdclass();
		$cssObjectClass->headerStyleArray = $headerStyleArray;
		$cssObjectClass->titleStyleArray = $titleStyleArray;
		$cssObjectClass->alignmentCenter = $alignmentCenter;
		$cssObjectClass->alignmentRight = $alignmentRight;
		$cssObjectClass->boldResultArray = $boldResultArray;
		return $cssObjectClass;
	}
    
}