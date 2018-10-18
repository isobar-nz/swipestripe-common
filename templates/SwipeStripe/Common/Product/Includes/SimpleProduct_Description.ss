<% if $ShortDescription %>
    <p>{$ShortDescription}</p>
<% else_if $Content %>
    <p>{$Content.Summary}</p>
<% end_if %>
