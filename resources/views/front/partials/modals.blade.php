@if (Session::has('message'))
    <div class="modals">
        <div class="modal" id="successFeedback">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="">
                        <div class="close" data-dismiss="modal"></div>
                        <p>
                            {{ Session::get('message') }}
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<script type="text/javascript">
    $(window).on('load',function(){
        $('#successFeedback').modal('show');
    });
</script>
