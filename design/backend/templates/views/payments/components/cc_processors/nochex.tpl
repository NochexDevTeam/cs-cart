<div class="control-group">
    <label class="control-label" for="">{__("merchant_id")}:</label>
    <div class="controls">
    	<input type="text" name="payment_data[processor_params][merchantid]" value="{$processor_params.merchantid}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="">{__("payment_details")}:</label>
    <div class="controls">
    	<input type="text" name="payment_data[processor_params][payment_description]" value="{$processor_params.payment_description}"   size="60">
    </div>
</div>

  
<div class="control-group">
    <label class="control-label" for="mode">Test Mode:</label>
    <div class="controls">
        <select name="payment_data[processor_params][testmode]" id="mode">
            <option value="test" {if $processor_params.testmode == "test"}selected="selected"{/if}>{__("test")}</option>
            <option value="live" {if $processor_params.testmode == "live"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">Product Collection:</label>
    <div class="controls">
        <select name="payment_data[processor_params][xmlMode]" id="mode">
            <option value="Yes" {if $processor_params.xmlMode == "Yes"}selected="selected"{/if}>{__("Yes")}</option>
            <option value="No" {if $processor_params.xmlMode == "No"}selected="selected"{/if}>{__("No")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">Postage:</label>
    <div class="controls">
        <select name="payment_data[processor_params][postAmt]" id="mode">
            <option value="Yes" {if $processor_params.postAmt == "Yes"}selected="selected"{/if}>{__("Yes")}</option>
            <option value="No" {if $processor_params.postAmt == "No"}selected="selected"{/if}>{__("No")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">Hide Billing Details:</label>
    <div class="controls">
        <select name="payment_data[processor_params][HideM]" id="mode">
            <option value="Yes" {if $processor_params.HideM == "Yes"}selected="selected"{/if}>{__("Yes")}</option>
            <option value="No" {if $processor_params.HideM == "No"}selected="selected"{/if}>{__("No")}</option>
        </select>
    </div>
</div>