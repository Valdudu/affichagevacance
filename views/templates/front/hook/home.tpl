
{if ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' > $vacance.from && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' < $vacance.to) || $smarty.server.REMOTE_ADDR|in_array:$vacance.ip_list}
	<div class="holidays container">
		<div class="row">
		    <div class="col-md-12">
		        <div style="background:#f03e94;color:#fff;padding:15px;border-radius:8px;margin:15px auto;font-size:12px;">
		  	        {$vacance.text nofilter}
	            </div>
            </div>
        </div>
    </div>
{/if}