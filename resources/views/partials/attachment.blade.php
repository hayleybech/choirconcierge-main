<div class="r-table__row">
    <div class="r-table__cell column--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell column--title">
        {{ ( isset($attachment->title) ) ? $attachment->title : 'Title Unknown' }}
    </div>
    <div class="r-table__cell column--filename">
        {{ $attachment->filepath }}
    </div>
    <div class="r-table__cell column--category">
        {{ $attachment->category->title }}
    </div>
    <div class="r-table__cell column--actions">
    </div>
</div>

