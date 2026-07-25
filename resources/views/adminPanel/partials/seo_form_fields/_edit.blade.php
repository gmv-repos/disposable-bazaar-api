<hr />
<b class="my-3">SEO Metadata</b>
<hr />
<div class="row editSeoMetadata">
    <div class="col-sm-6">
        <div class="mb-3">
            <label for="inputMetaTitle" class="form-label">Meta Title</label>
            <input type="text" class="form-control" name="meta_title" id="inputMetaTitle" placeholder="Enter Meta Title" value="{{ $seoMetadata->meta_title ?? '' }}">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label for="inputCanonicalUrl" class="form-label">Canonical URL</label>
            <input type="text" class="form-control" name="canonical_url" id="inputCanonicalUrl" placeholder="Enter Canonical URL" value="{{ $seoMetadata->canonical_url ?? '' }}">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label for="inputFocusKeyword" class="form-label">Focus Keyword</label>
            <input type="text" class="form-control" name="focus_keyword" id="inputFocusKeyword" placeholder="Enter Focus Keyword" value="{{ $seoMetadata->focus_keyword ?? '' }}">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label for="inputRedirect301" class="form-label">Redirect 301</label>
            <input type="text" class="form-control" name="redirect_301" id="inputRedirect301" placeholder="Enter Redirect 301 URL" value="{{ $seoMetadata->redirect_301 ?? '' }}">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label for="inputRedirect302" class="form-label">Redirect 302</label>
            <input type="text" class="form-control" name="redirect_302" id="inputRedirect302" placeholder="Enter Redirect 302 URL" value="{{ $seoMetadata->redirect_302 ?? '' }}">
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label for="inputSchema" class="form-label">Schema</label>
            <textarea class="form-control" name="schema" id="inputSchema" placeholder="Enter Schema">{{ $seoMetadata->schema ?? '' }}</textarea>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="mb-3">
            <label for="inputMetaDescription" class="form-label">Meta Description</label>
            <textarea class="form-control inputMetaDescriptionClass" name="meta_description" id="inputMetaDescription" placeholder="Enter Meta Description">

                                            {!! $seoMetadata->meta_description ?? '' !!}
                                            
                                            </textarea>
        </div>
    </div>


</div>