<?php
Yii::import('ext.bootstrap.widgets.TbEditableDetailView');

/**
 * See PtEditableColumn for details about this extension
 * @author Ryan
 *
 */
class PtEditableDetailView extends TbEditableDetailView
{
    protected function renderItem($options, $templateData)
    {
        //if editable set to false --> not editable
        $isEditable = array_key_exists('editable', $options) && $options['editable'] !== false;

        //if name not defined or it is not safe --> not editable
        $isEditable = !empty($options['name']) && $this->data->isAttributeSafe($options['name']);

        if ($isEditable) {    
            //ensure $options['editable'] is array
            if(!array_key_exists('editable', $options) || !is_array($options['editable'])) $options['editable'] = array();

            //take common url
            if (!array_key_exists('url', $options['editable'])) {
                $options['editable']['url'] = $this->url;
            }

            $editableOptions = CMap::mergeArray($options['editable'], array(
            	'success'   => 'js: function(data) {
    				if(typeof data == "object" && !data.success) return data.msg;  
				}',
                'model'     => $this->data,
                'attribute' => $options['name'],
                'emptytext' => ($this->nullDisplay === null) ? Yii::t('zii', 'Not set') : strip_tags($this->nullDisplay),
            ));
            
            //if value in detailview options provided, set text directly
            if(array_key_exists('value', $options) && $options['value'] !== null) {
                $editableOptions['text'] = $templateData['{value}'];
                $editableOptions['encode'] = false;
            }

            $templateData['{value}'] = $this->controller->widget('TbEditableField', $editableOptions, true);
        } 

        parent::renderItem($options, $templateData);
    }
}