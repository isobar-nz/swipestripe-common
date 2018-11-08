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

            <p>{$Description}</p>
            <h4>{$BasePrice.Nice}</h4>

            <% if $IsOutOfStock %>
                <strong><%t SwipeStripe\\Common\\Product\\ComplexProduct\\Layout\\ComplexProduct.OUT_OF_STOCK 'Out of stock' %></strong>
            <% else %>
                {$ComplexProductCartForm}
            <% end_if %>
        </section>

        <aside class="col-md-3">
            <% include SidebarNav %>
        </aside>
    </div>
</div>
<% include PageUtilities %>
