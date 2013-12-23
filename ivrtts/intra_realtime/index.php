<style>.label{margin:5px}</style>
<div class="row-fluid">
    <div clasS="grid">
        <div class="grid-title">
            <div class="pull-left">Totais</div>
            <div class="pull-right"></div>
        </div>
        <div class="grid-content">
            <div class="grid span10">
                <figure class="demo" id="Graph1" style="height: 300px;"></figure>
            </div>
            <div class="span2">
            <div class="grid">
                <label class="label label-info" >Total Chamadas</label>
                <label class="label label-success">Total Mensagens</label>
                <label class="label label-warning">Total Feedbacks de Sistema</label>
            </div>
            <div class="grid">
                <div class="grid-title"><div class="pull-left">Intervalo</div></div>
                <label class="label" id="intervaloTotais">  </label>
            </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="grid">
        <div class="grid-title">
            <div class="pull-left">Media da Duração da Chamada em Minutos</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <div class="grid span10">
                <figure class="demo" id="Graph2" style="height: 300px;"></figure>
            </div>
             <div class="span2">
            <div class="grid">
                <label class="label label-info" >Média Duração Chamada</label>
                <label class="label label-success">Média Duração Mensagem</label>
                <label class="label label-warning">Média Dur. Feedback de Sistema</label>
            </div>
            <div class="grid">
                <div class="grid-title"><div class="pull-left">Intervalo</div></div>
                <label class="label" id="intervaloAVG">  </label>
            </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Total Chamadas por Feedback</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <figure class="demo" id="Graph3" style="height: 300px;"></figure>
        </div>
    </div>
    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Duração total por Feedback</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <figure class="demo" id="Graph4" style="height: 300px;"></figure>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="grid span6 offset3">
        <div class="grid-title">
            <div class="pull-left">Feedbacks</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <div id="piechart" style="height:450px"></div>
        </div>
    </div>
</div>