<?php
Yii::import('ext.bootstrap.widgets.TbEditableColumn');
/**
 * 
 * We neede to extend EditableColumn to add the success js on line 20. Without this we would need to
 * manually add it to each column in TbGridView. This used in conjunction with:
 * 		public function actionUpdate()
		{
		    $es = new EditableSaver('User');
		    try {
		        $es->update();
		    } catch(CException $e) {
		        echo CJSON::encode(array('success' => false, 'msg' => $e->getMessage()));
		        return;
		    }
		    echo CJSON::encode(array('success' => true));
		}
 * Allows us to return proper JSON and thus not fire our default ajax error alert defined in main.php	
 * @author Ryan
 *
 */
class PtEditableColumn extends TbEditableColumn
{
    protected function renderDataCellContent($row, $data)
    {
        if(!$this->isEditable($data)) {
            parent::renderDataCellContent($row, $data);
            return; 
        }
        
        $options = CMap::mergeArray($this->editable, array(
            'success'   => 'js: function(data) {
        	if(typeof data == "object" && !data.success) return data.msg;
   			 }',
            'model'     => $data,
            'attribute' => $this->name,
        ));
        
        //if value defined for column --> use it as element text
        if(strlen($this->value)) {
            ob_start();
            parent::renderDataCellContent($row, $data);
            $text = ob_get_clean();
            $options['text'] = $text;
            $options['encode'] = false;
        }
       
        $editable = $this->grid->controller->createWidget('TbEditableField', $options);

        //manually make selector non unique to match all cells in column
        $selector = get_class($editable->model) . '_' . $editable->attribute;
        $editable->htmlOptions['rel'] = $selector;

        $editable->renderLink();

        //manually render client script (one for all cells in column)
        if (!$this->isScriptRendered) {
            $script = $editable->registerClientScript();
            Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $selector.'-event', '
                $("#'.$this->grid->id.'").parent().on("ajaxUpdate.yiiGridView", "#'.$this->grid->id.'", function() {'.$script.'});
            ');
            $this->isScriptRendered = true;
        }
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