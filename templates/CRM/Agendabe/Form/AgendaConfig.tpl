{* this template is used for AgendaBE Settings  *}
<div class="form-item">
<fieldset><legend>
{ts}AgendaBE Settings{/ts}
</legend>
<div class="crm-block crm-form-block">
<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
   <div class="form-layout-compressed">
    <span class="label">{ts}Select Event Types which needs to skip from xml.{/ts}</span>
        {foreach from=$event_type item="label" key="eventname"}
     <div class="crm-event-extension-{$label}">
            <span>{$form.$eventname.html}&nbsp;{$form.$eventname.label}<br />{$form.$label.html}</span>
        </div> 
     {/foreach}
     <span class="label">{ts}Select Event Status which needs to skip from xml.{/ts}</span>
     {foreach from=$event_statues item="label" key="eventname"}
     <div class="crm-event-extension-{$label}">
            <span>{$form.$eventname.html}&nbsp;{$form.$eventname.label}<br />{$form.$label.html}</span>
        </div> 
     {/foreach}
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>