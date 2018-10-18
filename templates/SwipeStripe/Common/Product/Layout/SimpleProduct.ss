<div class="container">
    <div class="row">
        <section class="col-md-10 col-md-offset-1">
            <div class="page-header">
                $Breadcrumbs
                <h1>$Title</h1>
            </div>
        </section>
    </div>
    <div class="row">
        <section class="col-md-10 col-md-offset-1">
            {$Content}

            <% if $ShortDescription %>
                <p>{$ShortDescription}</p>
            <% end_if %>

            <h4>{$Price.Nice}</h4>

            <a href="{$Link('AddMore')}?qty=1">Add To Cart</a>
        </section>

        <aside class="col-md-3">
            <% include SidebarNav %>
        </aside>
    </div>
</div>
<% include PageUtilities %>
