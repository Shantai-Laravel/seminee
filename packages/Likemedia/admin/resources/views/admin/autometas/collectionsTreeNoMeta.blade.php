<h5>Collections</h5>
<hr>
<div class="col-md-12">
    @foreach($collections as $key => $collection)
        <ul class="list-tree">
            <li>
                <label>
                  <input class="checkbox" type="checkbox" name="categories[]" value="{{ $collection->id }}">
                  <span>{{ $collection->translation()->first()->name }}</span>
                </label>
            </li>
        </ul>
    @endforeach
</div>

<script>
$(function() {
    let arr = [];
    $("input[type='checkbox']").change(function () {
        $(this).parent().siblings('ul')
               .find("input[type='checkbox']")
               .prop('checked', this.checked);
         $('input[type=checkbox]').each(function () {
             if($(this).prop('checked')) {
               if(!arr.includes($(this).val())) {
                 arr.push($(this).val());
               }
             } else {
               let index = arr.indexOf($(this).val());
               if (index !== -1) arr.splice(index, 1);
             }
         });
       $('#category_id').val(arr);
    });
});
</script>
