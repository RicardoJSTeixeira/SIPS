<?php
/**
 * Created by PhpStorm.
 * User: rteixeira
 * Date: 10-02-2015
 * Time: 13:10
 */

$a=array(
    array("Filtros:","Data Consulta"),
    array("","Data de criação da Consulta"),
    array("","ASM"),
    array("","Dispenser"),
    array("","Cod Campanha"),
    array("","Grupo de fecho (A, B, C, D, E)"),
    array("","Resultado de fecho (1, 2, 3, 4, 5, …)"),
    array(""),
    array("A","Não existe consulta","=1+2+3+4+5+6+7+8"),
    array("1","Desistiu - DEST",""),
    array("2","Remarcou - dá origem a marcar outra consulta - este feedback não fecha a consulta",""),
    array("3","Faleceu – FAL",""),
    array("4","Telefone Inválido - TINV",""),
    array("5","No Show - NOSHOW",""),
    array("6","Ninguém em Casa - NATEN (Novo)",""),
    array("7","Morada Errada - MOR (Novo)",""),
    array("8","Técnico não foi - NTEC (Novo)",""),
    array(""),
    array("B","Existe consulta mas não existe exame","=8+9+19+11+12+13+14"),
    array("8","Cera - LIMP",""),
    array("9","Otorrino - OTOR",""),
    array("10","Remarcou - dá origem a marcar outra consulta - este feedback não fecha a consulta",""),
    array("11","Assistência - SERV",""),
    array("12","Incapacitado - INC",""),
    array("13","Informações - PINF",""),
    array("14","Outros - OUT",""),
    array(""),
    array("C","Existe consulta com exame","=15+16+17"),
    array("15","Sem perda – SPERD – Termina consulta com resultado Sem perda",""),
    array("16","Perda - PERD",""),
    array("17","Perda power - PERDPW",""),
    array(""),
    array("D","Existe consulta com exame e tem perda (ou perda power) e tem venda","=18+19+20+21"),
    array("18","Tipo de venda: Monaural/Binaural (escolher uma das opções)	resultado por Binaural e Monoaural",""),
    array("19","Marca do aparelho (drop menu – falta a listagem)	resultado pela drop menu",""),
    array("20","Gama do aparelho (drop menu – falta a listagem)	resultado pela drop menu",""),
    array("21","Modelo do aparelho: ITE/BTE/RITE (drop menu)	resultado pela drop menu",""),
    array(""),
    array("E","Existe consulta com exame e tem perda (ou perda power) e não tem venda","=22+23+24+25+26+27"),
    array("22","Preço - PVP",""),
    array("23","Familiar - FAM",""),
    array("24","Sem aproveitamento - PAGU",""),
);

