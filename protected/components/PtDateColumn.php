<?php
Yii::import('zii.widgets.grid.CDataColumn');
/**
 * Renders a date in a fancy format
 * @author Ryan
 *
 */
class PtDateColumn extends CDataColumn{
	
	public $format='date';//Could be datetime
	
protected function renderDataCellContent($row,$data) 
{ 
	$date['day']=Yii::app()->dateFormatter->format('dd',$data[$this->name]);
    $date['month']=Yii::app()->dateFormatter->format('MMM',$data[$this->name]);
    $date['year']=Yii::app()->dateFormatter->format('yyyy',$data[$this->name]);
    $date['time']=Yii::app()->dateFormatter->format('HH:mm:ss',$data[$this->name]);

    $output.='<div class="well dateblock">';
    $output.= '<span class="month btn-primary">'.$date["month"].'</span>';
    $output.='<span class="day">'.$date["day"].'</span>';
    $output.= '<span class="year">'.$date["year"].'</span>';
    if($this->format=='datetime')
    $output.='<hr><span class="time">'.$date['time'].'</span>';
    $output.= '</div>'; 
    
    echo $output;
}

	/**
	 * Renders the header cell content.
	 * This method will render a link that can trigger the sorting if the column is sortable.
	 */
	protected function renderHeaderCellContent()
	{
		if ($this->grid->enableSorting && $this->sortable && $this->name !== null)
		{
			$sort = $this->grid->dataProvider->getSort();
			$label = isset($this->header) ? $this->header : $sort->resolveLabel($this->name);

			if ($sort->resolveAttribute($this->name) !== false)
				$label .= '<span class="caret"></span>';

			echo $sort->link($this->name, $label, array('class'=>'sort-link'));
		}
		else
		{
			if ($this->name !== null && $this->header === null)
			{
				if ($this->grid->dataProvider instanceof CActiveDataProvider)
					echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
				else
					echo CHtml::encode($this->name);
			}
			else
				parent::renderHeaderCellContent();
		}
	}
	
	
}