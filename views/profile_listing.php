<?php $this->view('partials/head'); ?>

<div class="container">
  <div class="row">
	<div class="col-lg-12">

	  <h3><span data-i18n="profile.report"></span> <span id="total-count" class='label label-primary'>…</span></h3>

	  <table class="table table-striped table-condensed table-bordered">
		<thead>
		  <tr>
			<th data-i18n="listing.computername" data-colname='machine.computer_name'></th>
			<th data-i18n="serial" data-colname='reportdata.serial_number'></th>
            <th data-i18n="profile.profilename" data-colname='profile.profile_name'></th>
            <th data-i18n="profile.uuid" data-colname='profile.profile_uuid'></th>
            <th data-i18n="profile.scope" data-colname='profile.user'></th>
            <th data-i18n="profile.method" data-colname='profile.method'></th>
            <th data-i18n="profile.payload_type" data-colname='profile.payload_name'></th>
            <th data-i18n="profile.payloadname" data-colname='profile.payload_display'></th>
            <th data-i18n="profile.payload_data" data-colname='profile.timestamp'></th>
            <th data-i18n="profile.profile_removal_allowed" data-colname='profile.profile_removal_allowed'></th>		      	
            <th data-i18n="profile.profile_install_date" data-colname='profile.profile_install_date'></th>
            <th data-i18n="profile.profile_organization" data-colname='profile.profile_organization'></th>
            <th data-i18n="profile.profile_verification_state" data-colname='profile.profile_verification_state'></th>
            <th data-i18n="profile.profile_description" data-colname='profile.profile_description'></th>
		  </tr>
		</thead>

		<tbody>
		  <tr>
			<td data-i18n="listing.loading" colspan="13" class="dataTables_empty"></td>
		  </tr>
		</tbody>
	  </table>
	</div> <!-- /span 12 -->
  </div> <!-- /row -->
</div>  <!-- /container -->

<script type="text/javascript">

	$(document).on('appUpdate', function(e){

		var oTable = $('.table').DataTable();
		oTable.ajax.reload();
		return;

	});

	$(document).on('appReady', function(e, lang) {

        // Get modifiers from data attribute
        var mySort = [], // Initial sort
            hideThese = [], // Hidden columns
            col = 0, // Column counter
            runtypes = [], // Array for runtype column
            columnDefs = [{ visible: false, targets: hideThese }]; //Column Definitions

        $('.table th').map(function(){

            columnDefs.push({name: $(this).data('colname'), targets: col, render: $.fn.dataTable.render.text()});

            if($(this).data('sort')){
              mySort.push([col, $(this).data('sort')])
            }

            if($(this).data('hide')){
              hideThese.push(col);
            }

            col++
        });

	    oTable = $('.table').dataTable( {
            ajax: {
                url: appUrl + '/datatables/data',
                type: "POST",
                data: function(d){
                     d.mrColNotEmpty = "profile_uuid";

                    // Check for column in search
                    if(d.search.value){
                        $.each(d.columns, function(index, item){
                            if(item.name == 'profile.' + d.search.value){
                                d.columns[index].search.value = '> 0';
                            }
                        });

                    }
                }
            },
            dom: mr.dt.buttonDom,
            buttons: mr.dt.buttons,
            order: mySort,
            columnDefs: columnDefs,
		    createdRow: function( nRow, aData, iDataIndex ) {
	        	// Update name in first column to link
	        	var name=$('td:eq(0)', nRow).html();
	        	if(name == ''){name = "No Name"};
	        	var sn=$('td:eq(1)', nRow).html();
	        	var link = mr.getClientDetailLink(name, sn, '#tab_profile-tab');
	        	$('td:eq(0)', nRow).html(link);

                // payload_display
                var payload_display=$('td:eq(6)', nRow).text();
                (payload_display = payload_display == 'No Payload Display Name' ? i18n.t('') : payload_display)
                $('td:eq(6)', nRow).text(payload_display)

                // View payload data button
                var profile_name=$('td:eq(2)', nRow).text();
                var profile_uuid=$('td:eq(3)', nRow).text();
                var payload_type=$('td:eq(5)', nRow).text();
	        	$('td:eq(7)', nRow).html('<button onclick="view_payload_data(\''+sn+'\',\''+profile_uuid+'\',\''+payload_type+'\',\''+profile_name+'\')" class="btn btn-info btn-xs" style="min-width: 100px;" >'+i18n.t('profile.view')+'</button>')

                // profile_removal_allowed
                var removal_allowed=$('td:eq(8)', nRow).text();
                removal_allowed = removal_allowed == '' ? i18n.t('yes') :
                removal_allowed = removal_allowed == 'yes' ? i18n.t('yes') :
                removal_allowed = removal_allowed == 'false' ? i18n.t('yes') :
                removal_allowed = removal_allowed == 'allowed' ? i18n.t('yes') :
                removal_allowed = removal_allowed == 'None' ? i18n.t('') :
                removal_allowed = removal_allowed == 'disallowed' ? i18n.t('no') :
                removal_allowed = removal_allowed == 'true' ? i18n.t('no') :
                (removal_allowed = removal_allowed == 'no' ? i18n.t('no') : removal_allowed)
                $('td:eq(8)', nRow).text(removal_allowed)

                // Format profile_install_date
                var event = parseInt($('td:eq(9)', nRow).text());
                if (event){
                    var date = new Date(event * 1000);
                    $('td:eq(9)', nRow).html('<span title="' + moment(date).fromNow() + '">'+moment(date).format('llll')+'</span>');
                }

                // profile_verification_state
                var verification_state=$('td:eq(11)', nRow).text();
                verification_state = verification_state == 'verified' ? i18n.t('profile.verified') :
                verification_state = verification_state == 'signed' ? i18n.t('profile.verified') :
                verification_state = verification_state == 'not verified' ? i18n.t('profile.not_verified') :
                (verification_state = verification_state == 'unsigned' ? i18n.t('profile.not_verified') : verification_state)
                $('td:eq(11)', nRow).text(verification_state)
		    }
	    });
	});

    // Get payload data via API and display in modal
    function view_payload_data(serial_number, profile_uuid, payload_type, profile_name){
        $.get(appUrl + '/module/profile/get_payload_data/'+serial_number+'/'+profile_uuid+'/'+payload_type, function(data, status){

            // Create large modal
            $('#myModal .modal-dialog').addClass('modal-lg');
            $('#myModal .modal-title')
                .empty()
                .append(profile_name+' - '+payload_type)
            $('#myModal .modal-body')
                .empty()
                .append(data.replace(/\n/g, '<br>'));

            $('#myModal button.ok').text(i18n.t("dialog.close"));

            // Set ok button
            $('#myModal button.ok')
                .off()
                .click(function(){$('#myModal').modal('hide')});

            $('#myModal').modal('show');
        });
    }
</script>

<?php $this->view('partials/foot'); ?>
