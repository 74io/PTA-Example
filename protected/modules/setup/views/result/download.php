<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}<br/>{%#file.errorDetail%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
				<div id="result-sample">{% var decoded=$("<div/>").html(file.resultSample).text(); print(decoded,true); %}</div>
            {% } %}</td>
            <td class="name">
                <span class="label label-success">Success</span>
  				<p>{%=file.resultNumRecords%} records from your file {%=file.name%} have been imported.</p>
				<p><i class="icon-chevron-left"></i>
				Here's a sample of the file you just imported.</p><p> If it is not what
				you expected then click the delete button and try again.</p>
				
				<?php $this->widget('bootstrap.widgets.TbButton', array(
    			'label'=>'Import another file',
    			'type'=>'primary',
				'icon' => 'icon-plus icon-white',
				'url'=> $this->controller->createUrl('result/import')
    			)); ?>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete" width="100px">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>{%=locale.fileupload.destroy%}</span>
            </button>
        </td>
    </tr>
{% } %}
</script>

