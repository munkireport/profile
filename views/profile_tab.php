<div id="profile-tab"></div>
<h2 data-i18n="profile.profile"></h2>
<div id="profile-msg" data-i18n="listing.loading" class="col-lg-12 text-center"></div>

<div id="profile-table-view" class="row hide" style="padding-left: 15px; padding-right: 15px;">
  <table class="table table-striped table-condensed table-bordered" id="profile-table">
    <thead>
      <tr>
        <th data-i18n="profile.profilename" data-colname='profile.profile_name'></th>
        <th data-i18n="profile.uuid" data-colname='profile.profile_uuid'></th>
        <th data-i18n="profile.scope" data-colname='profile.user'></th>
        <th data-i18n="profile.method" data-colname='profile.profile_method'></th>
        <th data-i18n="profile.payload_type" data-colname='profile.payload_name'></th>
        <th data-i18n="profile.payloadname" data-colname='profile.payload_display'></th>
        <th data-i18n="profile.payload_data" data-colname='profile.serial_number'></th>
        <th data-i18n="profile.profile_removal_allowed" data-colname='profile.profile_removal_allowed'></th>		      	
        <th data-i18n="profile.profile_install_date" data-colname='profile.profile_install_date'></th>
        <th data-i18n="profile.profile_organization" data-colname='profile.profile_organization'></th>
        <th data-i18n="profile.profile_verification_state" data-colname='profile.profile_verification_state'></th>
        <th data-i18n="profile.profile_description" data-colname='profile.profile_description'></th>
      </tr>
    </thead>
    <tbody>
        <tr>
            <td data-i18n="listing.loading" colspan="11" class="dataTables_empty"></td>
        </tr>
    </tbody>
  </table>
</div>


<script>
    $(document).on('appReady', function(e, lang) {

        // Get profile data
        $.getJSON( appUrl + '/module/profile/get_data/' + serialNumber, function( data ) {
            // Check if we have data
            if( ! data || data.length == 0){
                $('#profile-msg').text(i18n.t('profile.noprofile'));
                $('#profile-cnt').text(0);

            } else {
               // Hide
                $('#profile-msg').text('');
                $('#profile-table-view').removeClass('hide');

                // Set count of profiles
                $('#profile-cnt').text(data.length);

                // Build out the profile table data
                $('#profile-table').DataTable({
                    data: data,
                    order: [[0,'asc']],
                    autoWidth: false,
                    columns: [
                        { data: 'profile_name' },
                        { data: 'profile_uuid' },
                        { data: 'user' },
                        { data: 'payload_name' },
                        { data: 'payload_display' },
                        { data: 'serial_number' },
                        { data: 'profile_removal_allowed' },
                        { data: 'profile_install_date' },
                        { data: 'profile_organization' },
                        { data: 'profile_verification_state' },
                        { data: 'profile_description' }
                    ],
                    createdRow: function( nRow, aData, iDataIndex ) {

                        // payload_display
                        var payload_display=$('td:eq(5)', nRow).text();
                        (payload_display = payload_display == 'No Payload Display Name' ? i18n.t('') : payload_display)
                        $('td:eq(5)', nRow).text(payload_display)

                        // View payload data button
                        var profile_name=$('td:eq(0)', nRow).text();
                        var profile_uuid=$('td:eq(1)', nRow).text();
                        var payload_type=$('td:eq(4)', nRow).text();
                        var sn=$('td:eq(6)', nRow).text();
                        $('td:eq(6)', nRow).html('<button onclick="view_payload_data(\''+sn+'\',\''+profile_uuid+'\',\''+payload_type+'\',\''+profile_name+'\')" class="btn btn-info btn-xs" style="min-width: 100px;" >'+i18n.t('profile.view')+'</button>')

                        // profile_removal_allowed
                        var removal_allowed=$('td:eq(7)', nRow).text();
                        removal_allowed = removal_allowed == '' ? i18n.t('yes') :
                        removal_allowed = removal_allowed == 'yes' ? i18n.t('yes') :
                        removal_allowed = removal_allowed == 'false' ? i18n.t('yes') :
                        removal_allowed = removal_allowed == 'allowed' ? i18n.t('yes') :
                        removal_allowed = removal_allowed == 'None' ? i18n.t('') :
                        removal_allowed = removal_allowed == 'disallowed' ? i18n.t('no') :
                        removal_allowed = removal_allowed == 'true' ? i18n.t('no') :
                        (removal_allowed = removal_allowed == 'no' ? i18n.t('no') : removal_allowed)
                        $('td:eq(7)', nRow).text(removal_allowed)

                        // Format profile_install_date
                        var event = parseInt($('td:eq(8)', nRow).text());
                        if (event){
                            var date = new Date(event * 1000);
                            $('td:eq(8)', nRow).html('<span title="' + moment(date).fromNow() + '">'+moment(date).format('llll')+'</span>');
                        }

                        // profile_verification_state
                        var verification_state=$('td:eq(10)', nRow).text();
                        verification_state = verification_state == 'verified' ? i18n.t('profile.verified') :
                        verification_state = verification_state == 'signed' ? i18n.t('profile.verified') :
                        verification_state = verification_state == 'not verified' ? i18n.t('profile.not_verified') :
                        (verification_state = verification_state == 'unsigned' ? i18n.t('profile.not_verified') : verification_state)
                        $('td:eq(10)', nRow).text(verification_state)
                    }
                });
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
