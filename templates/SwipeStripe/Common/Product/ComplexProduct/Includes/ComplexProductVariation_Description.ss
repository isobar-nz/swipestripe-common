<% if $Product.Description %>
    <p>{$Product.Description}</p>
<% else_if $Product.Content %>
    <p>{$Product.Content.Summary}</p>
<% end_if %>

<% if $ProductAttributeOptions %>
    <ul>
        <% loop $ProductAttributeOptions %>
            <li>
                <strong>{$ProductAttribute.Title}:</strong>
                {$Title}
            </li>
        <% end_loop %>
    </ul>
<% end_if %>
