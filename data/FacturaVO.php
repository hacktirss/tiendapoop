<?php

/*
 * FacturaVO
 * omicrom®
 * © 2017, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since jul 2017
 */

include_once ('CiaVO.php');
include_once ('CliVO.php');
include_once ('RelacionesVO.php');
include_once ('FcVO.php');

class FacturaVO {
    /* @var $emisor CiaVO */
    private $emisor;
    /* @var $receptor CliVO */
    private $receptor;
    /* @var $comprobante FcVO */
    private $comprobante;
    /* @var $conceptos array */
    private $conceptos;
    /* @var $relacion RelacionesVO */
    private $relacion;

    /** @var $metodoDePago MetodoDePagoVO */
    private $metodoDePago;
    /** @var $tipoDocumento String */
    private $tipoDocumento = "FA";

    /**
     * 
     * @return CiaVO
     */
    function getEmisor() {
        return $this->emisor;
    }

    /**
     * 
     * @return CliVO
     */
    function getReceptor() {
        return $this->receptor;
    }

    /**
     * 
     * @return FcVO
     */
    function getComprobante() {
        return $this->comprobante;
    }

    /**
     * 
     * @return array
     */
    function getConceptos() {
        return $this->conceptos;
    }

    function getRelacion() {
        return $this->relacion;
    }

    function getMetodoDePago() {
        return $this->metodoDePago;
    }

    function getTipoDocumento() {
        return $this->tipoDocumento;
    }

    function setEmisor($emisor) {
        $this->emisor = $emisor;
    }

    function setReceptor($receptor) {
        $this->receptor = $receptor;
    }

    function setComprobante($comprobante) {
        $this->comprobante = $comprobante;
    }

    function setConceptos($conceptos) {
        $this->conceptos = $conceptos;
    }
    
    function setRelacion($relacion) {
        $this->relacion = $relacion;
    }

    function setMetodoDePago($metodoDePago) {
        $this->metodoDePago = $metodoDePago;
    }

    function setTipoDocumento($tipoDocumento) {
        $this->tipoDocumento = $tipoDocumento;
    }

    private function cZeros($Vlr, $nLen, $Position = "") {
        $Position = strtoupper($Position);
        if ($Position == "" || $Position == "LEFT") {
            for ($i = strlen($Vlr); $i < $nLen; $i = $i + 1) {
                $Vlr = "0" . $Vlr;
            }
        } elseif($Position == "RIGHT") {
            for ($i = strlen($Vlr); $i < $nLen; $i = $i + 1) {
                $Vlr .= "0";
            }
        }
        return $Vlr;
    }//cZeros

    function getCfdi32Pipes() {
        // Inicializa los totalizadores
        $Subtotal = 0;
        $Total = 0;

        $IRTotal     = 0;   // Impuesto retenido total
        $ITTotal     = 0;   // Impuesto trasladado total

        $ITIEPSTotal = 0;   // IEPS trasladado total
        $IRIVATotal  = 0;   // IVA retenido total
        $IRISRTotal  = 0;   // ISR retenido total

        $RTasaISR    = 0;   // Tasa IEPS

        $ItemIndex = 0;

        $Detalle = "";

        $ItemIndex = 0;
        /* @var $concepto FacturaConceptoVO */
        foreach ($this->conceptos as $concepto) {
            $stotal       = round($concepto->getCantidad() * $concepto->getPrecio(), 2);
            $Subtotal    += $stotal;

            $Total       += round($concepto->getTotal(), 2);

            // Traslados totales
            $ITTotal     += $concepto->getImpiva() + $concepto->getImpieps(); // + $concepto->getImpish();
            // Retenciones totales
            $IRTotal     += $concepto->getRetencioniva() + $concepto->getImpisr();

            $IRIVATotal  += round($concepto->getRetencioniva(), 2);
            $ITIEPSTotal += round($concepto->getImpieps(), 2);
            $IRISRTotal  += round($concepto->getImpisr(), 2);
            $RTasaISR = ($IRISRTotal > 0 ? $concepto->getFactorieps() : 0);

            $cDescripcion = ucwords(strtolower($concepto->getDescripcion()));

            $Concepto = "|" . (++$ItemIndex)
                    . "|" . round($concepto->getCantidad(), 3)
                    . "|" . $concepto->getUmedida()
                    . "|" . $concepto->getClave()
                    . "|" . $cDescripcion
                    . "|" . round($concepto->getPrecio(), 6)
                    . "|" . ""                                                  // Descuento
                    . "|" . round($concepto->getCantidad() 
                            * $concepto->getPrecio(), 2)
                    . "|" . ""                                                  // Pedimento
                    . "|" . ""                                                  // Fecha Pedimento
                    . "|" . ""                                                  // Aduana
                    . "|" . ""                                                  // Número Predial
                    . "|TRASLADADOS|IVA|" . round($concepto->getFactoriva() * 100, 0)
                    . "|" . round($concepto->getImpiva(), 4)
                    . "|IEPS|" . round($concepto->getFactorieps(), 4)
                    . "|" . round($concepto->getImpieps(), 4);
            if ($IRTotal > 0) {
                $Concepto = $Concepto 
                    . "|RETENIDOS"
                    . ($concepto->getImpisr()>0 ? "|ISR|" 
                            . round($concepto->getFactorisr() * 100, 0) 
                            . round($concepto->getImpisr(), 4) : "")
                    . ($concepto->getRetencioniva()>0 ? "|IVA|" 
                            . round($concepto->getFactoriva() * 100, 0) 
                            . round($concepto->getRetencioniva(), 4) : "");
            }// Si hay retenciones

            $Detalle = $Detalle . $Concepto;
        }//foreach concepto

        $Partidas = $this->cZeros($ItemIndex, 2);
        $Detalle = "|" . $Partidas . "|" . $Detalle;

        $Datos = "01"                                                           // Encabezado;
            . "|" . $this->tipoDocumento                                        // Factura,Nota de Credito...
            . "|" . "3.2"                                                       // Version del CFDI
            . "|" . ""
            . "|" . $this->comprobante->getFolio()
            . "|" . "PAGO EN UNA SOLA EXHIBICION" 
            . "|" . ""                                                          // Numero de certificado aqui no mando nada
            . "|" . ""                                                          // Condiciones de pago NO MANDO NADA
            . "|" . round($Subtotal, 4)
            . "|" . ""                                                          // Descuento
            . "|" . ""                                                          // Descripcion del Motivo del descuento
            . "|" . round($Total, 4)
            . "|" . ($this->receptor->getFormadepago()=="98" ? 
                    "NA" : $this->receptor->getFormadepago())
            . "|" . "ingreso"
            . "|" . "MXN"
            . "|" . "1"                                                         // Tipo de cambio 1;
            . "|EMISOR"
            . "|" . $this->emisor->getRfc()
            . "|" . $this->emisor->getNombre()
            . "|DOMICILIO FISCAL"
            . "|" . $this->emisor->getDireccion()
            . "|" . $this->emisor->getNumeroext()
            . "|" . $this->emisor->getNumeroint()
            . "|" . $this->emisor->getColonia()
            . "|" . $this->emisor->getMunicipio()
            . "|" . "TEL. " . $this->emisor->getTelefono()                      // Referencia de la localidad o/y tel
            . "|" . $this->emisor->getMunicipio()
            . "|" . $this->emisor->getEstado()
            . "|MEXICO"
            . "|" . $this->emisor->getCodigo()
            . "|EXPEDIDO"
            . "|" . $this->emisor->getDireccione()
            . "|" . $this->emisor->getNumeroexte()
            . "|" . $this->emisor->getNumerointe()
            . "|" . $this->emisor->getColoniae()
            . "|" . ""                                                          // Localidad y Ref
            . "|" . ($this->emisor->getMunicipioe()=="" ? 
                    $this->emisor->getMunicipio() : $this->emisor->getMunicipioe())
            . "|" . ($this->emisor->getEstadoe()=="" ? 
                    $this->emisor->getEstado() : $this->emisor->getEstadoe())
            . "|MEXICO"
            . "|" . $this->emisor->getCodigo()
            . "|RECEPTOR"
            . "|" . strtoupper($this->receptor->getRfc())
            . "|" . strtoupper($this->receptor->getNombre())
            . "|DOMICILIO_FISCAL"
            . "|" . $this->receptor->getDireccion()
            . "|" . $this->receptor->getNumeroext()
            . "|" . $this->receptor->getNumeroint()
            . "|" . $this->receptor->getColonia()
            . "|" . ""                                                          // Localidad
            . "|" . $this->receptor->getMunicipio()                             // Referencia
            . "|" . ""                                                          // Sin uso
            . "|" . $this->receptor->getMunicipio()
            . "|" . strtoupper($this->receptor->getEstado())
            . "|" . $this->receptor->getPais()
            . "|" . $this->receptor->getCodigo()
            . "|" . $this->receptor->getCorreo()
            . "|" . $IRTotal                                                    //Total de impuesto retenido
            . "|" . round($ITTotal, 2)
            . "|" . ($IRTotal > 0 ? "RETENIDOS" : "")                           //Texto en caso de que haya retencion solo se pone RETENIDOS
            . "|" . "IVA"
            . "|" . $this->emisor->getIva()
            . "|" . $IRIVATotal                                                 //Importe del iva retenido
            . "|" . ($IRISRTotal > 0 ? "ISR" : "")                              //SI es que hay isr pone la palabra Isr
            . "|" . ($IRISRTotal > 0 ? $RTasaISR : "")                          //Tasa impuesto isr
            . "|" . ($IRISRTotal > 0 ? $IRISRTotal : "")                        //Importe del Isr
            . "|" . $this->emisor->getCiudad() . " " 
                  . $this->emisor->getEstado()                                  //Lugar donde se expide el comprobante
            . "|" . $this->emisor->getRegimen()
            . "|" . $this->receptor->getCuentaban()
            . "|" . ""
            . "|" . $this->comprobante->getObservaciones()
            . "|" . $this->emisor->getPublicidad()                              // Publicidad
            . "|" . $this->comprobante->getFecha()
            . "|INI_PRODUCTOS";

        $Datos = $Datos . $Detalle;
        
        return $Datos;
    }//getCfdi32Pipes

    function getCfdi33Json() {

        // Inicializa los totalizadores
        $Subtotal   = 0.00;
        $Descuento  = 0.00;
        $Total      = 0.00;

        $ITTotal        = 0.00;     // Impuesto trasladado total
        $IRTotal        = 0.00;     // Impuesto retenido total

        $ITIVATotal     = 0.00;     // IVA   trasladado total
        $ITIEPSTotal    = 0.00;     // IEPS trasladado total
        $ITISHTotal     = 0.00;    // ISH trasladado total

        $IRISRTotal     = 0.00;     // ISR retenido total
        $IRIVATotal     = 0.00;     // IEPS trasladado total

        $TTasaIVA  = 0;
        $TTasaIEPS = 0;
        $TTasaISH = 0;

        $TTasaISR  = 0;
        $TTasaRIVA = 0;

        $conceptos = array();

        // Impuestos trasladados
        $timpuestos = array();
        // Impuestos retenidos
        $rimpuestos = array();

        /* @var $concepto FacturaConceptoVO */
        foreach ($this->conceptos as $concepto) {

            $Subtotal       += round($concepto->getSubtotal(), 2);
            $Descuento      += round($concepto->getDescuento(), 2);
            $Total          += round($concepto->getTotal(), 2);

            /******************  RETENCIONES **********************************/
            $TTasaISR       = $concepto->getFactorisr();
            $TTasaRIVA      = $concepto->getFactorisr();

            $IRISRTotal     += round($concepto->getImpisr(), 2);
            $IRIVATotal     += round($concepto->getRetencioniva(), 2);

            $IRTotal        += round($concepto->getRetencioniva(), 2) + round($concepto->getImpisr(), 2);

            if (array_key_exists('IVA', $rimpuestos)) {
                $rimpuestos['IVA']['Importe']   += round($concepto->getRetencioniva(), 2);
            } else {
                $rimpuestos['IVA'] = array(
                    'Impuesto'      => '002',
                    'TipoFactor'    => 'Tasa',
                    'TasaOCuota'    => $TTasaIVA,
                    'Importe'       => round($concepto->getRetencioniva(), 2)
                );
            }

            if (array_key_exists('ISR', $rimpuestos)) {
                $rimpuestos['ISR']['Importe']   += round($concepto->getImpisr(), 2);
            } else {
                $rimpuestos['ISR'] = array(
                    'Impuesto'      => '001',
                    'TipoFactor'    => 'Tasa',
                    'TasaOCuota'    => $TTasaISR,
                    'Importe'       => round($concepto->getImpisr(), 2)
                );
            }

            $retenciones = array();
            if ($concepto->getSubtotal()>0) {
                $rIva = array(
                    'Base' => round($concepto->getBase(), 2),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $concepto->getFactoriva(),
                    'Importe' => round($concepto->getRetencioniva(), 2) 
                );

                $rIsr = array(
                    'Base' => round($concepto->getBase(), 2),
                    'Impuesto' => '001',
                    'TipoFactor' => 'Cuota',
                    'TasaOCuota' => $concepto->getFactorisr(),
                    'Importe' => round($concepto->getImpisr(), 2) 
                );

                if ($rIva['Importe']>0) {
                    array_push($retenciones, $rIva);
                }

                if ($rIsr['Importe']>0) {
                    array_push($retenciones, $rIsr);
                }
            }

            /******************  TRASLADOS   **********************************/
            $TTasaIVA       = $concepto->getFactoriva();
            $TTasaIEPS      = $concepto->getFactorieps();
            $TTasaISH       = $concepto->getFactorisr();

            $ITIVATotal     += round($concepto->getImpieps(), 2);
            $ITIEPSTotal    += round($concepto->getImpieps(), 2);
            $ITISHTotal     += round($concepto->getImpish(), 2);

            $ITTotal        += round($concepto->getImpiva(), 2) + round($concepto->getImpieps(), 2) + round($concepto->getImpish(), 2);

            $cDescripcion   = ucwords(strtolower($concepto->getDescripcion()));

            if (array_key_exists('IVA', $timpuestos)) {
                $timpuestos['IVA']['Importe']   += round($concepto->getImpiva(), 2);
            } else {
                $timpuestos['IVA'] = array(
                    'Impuesto'      => '002',
                    'TipoFactor'    => 'Tasa',
                    'TasaOCuota'    => $TTasaIVA,
                    'Importe'       => round($concepto->getImpiva(), 2)
                );
            }

            if (array_key_exists($TTasaIEPS, $timpuestos)) {
                $timpuestos[$TTasaIEPS]['Importe']   += round($concepto->getImpieps(), 2);
            } else {
                $timpuestos[$TTasaIEPS] = array(
                    'Impuesto'      => '003',
                    'TipoFactor'    => 'Cuota',
                    'TasaOCuota'    => $TTasaIEPS,
                    'Importe'       => round($concepto->getImpieps(), 2)
                );
            }

            if (array_key_exists('ISH', $timpuestos)) {
                $timpuestos['ISH']['Importe']   += round($concepto->getImpish(), 2);
            } else {
                $timpuestos['ISH'] = array(
                    'Impuesto'      => '004',
                    'TipoFactor'    => 'Tasa',
                    'TasaOCuota'    => $TTasaISH,
                    'Importe'       => round($concepto->getImpish(), 2)
                );
            }

            $traslados = array();
            if ($concepto->getSubtotal()>0) {
                $tIva = array(
                    'Base' => round($concepto->getBase(), 2),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $concepto->getFactoriva(),
                    'Importe' => round($concepto->getImpiva(), 2) 
                );

                $tIeps = array(
                    'Base' => round($concepto->getCantidad(), 2),
                    'Impuesto' => '003',
                    'TipoFactor' => 'Cuota',
                    'TasaOCuota' => $concepto->getFactorieps(),
                    'Importe' => round($concepto->getImpieps(), 2) 
                );

                $tIsh = array(
                    'Base' => round($concepto->getBase(), 2),
                    'Impuesto' => '004',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $concepto->getFactorish(),
                    'Importe' => round($concepto->getImpish(), 2) 
                );

                array_push($traslados, $tIva);
                if ($tIeps['Importe']>0) {
                    array_push($traslados, $tIeps);
                }
            }

            $concepto = array(
                'claveProducto' => $concepto->getInv_cproducto(),                                               // Clave de Producto, viene de CClaveProdServ
                'noIdentificacion'=> $concepto->getClave(),                                                     // Número de producto o servicio, SKU, clave o equivalente propio del Emisor
                'cantidad' => round($concepto->getCantidad(), 3),                                               // Canitidad de producto
                'claveUnidad' => trim($concepto->getInv_cunidad()),                                             // Clave de la unidad de medida empleada, viene de CClaveUnidad
                'descripcion' => $cDescripcion,                                                                 // Descripcion del bien o servicio
                'valorUnitario' => round($concepto->getPrecio(), 2),                                            // Costo unitario antes de impuestos y descuentos
                'importe' => round($concepto->getCantidad() * $concepto->getPrecio(), 2),                       // Importe total, debe resultar de multiplicar el costo unitario por la cantidad
                'descuento' => round($concepto->getDescuento(), 2),
                'traslados' => $traslados,
                'retenciones' => $retenciones
            );

            array_push($conceptos, $concepto);
        }//foreach concepto

        error_log("Agregando las retenciones");
        $gretenciones = array();
        foreach ($rimpuestos as $key=>$value) {
            error_log("Impuesto Retenido " . $value['Impuesto'] . " importe " . $value['Importe']);
            if ($value['Importe']>0) {
                array_push($gretenciones, $value);
            }
        }

        error_log("Agregando los traslados");
        $gtraslados = array();
        foreach ($timpuestos as $key=>$value) {
            error_log("Impuesto Trasladado " . $value['Impuesto'] . " importe " . $value['Importe']);
            if ($value['Importe']>0) {
                array_push($gtraslados, $value);
            }
        }

        $cfdiRelacionados = NULL;
        if ($this->relacion->hasRelated()) {
            $relaciones = array();
            $relacion = array(
                'uuid'=>$this->relacion->getUuid()
            );
            array_push($relaciones, $relacion);
            $cfdiRelacionados = array(
                'tipo'=>$this->relacion->getTipoRelacion(),
                'cfdis'=>$relaciones
            );
        }

        $cfdi= array(
          'tipo'=> $this->tipoDocumento,                                            // Tipo de CFDI (Omicrom)
          'version' => "3.3",                                                       // Versión del CFDI *
          'serie' =>  "",                                                           // Serie del CFDI
          'cfdiRelacionados' => $cfdiRelacionados,                                  // Serie del CFDI
          'folio' => $this->comprobante->getFolio(),                                // Folio del CDFI
          'fecha' => $this->comprobante->getFecha(),                                // Fecha de emisi�n del comprobante
          'metodoDePago' => $this->receptor->getTipodepago(),                       // Método de pago, viene de CMetodoPago *
          'subTotal' => round($Subtotal, 2),                                        // Sub total. Total antes de impuestos y descuentos.
          'descuento' => $Descuento,                                                // Descuento
          'total' => round($Total, 2),                                              // Total despues del impuestos y descuentos
          'formaDePago' => ($this->receptor->getFormadepago()=='98' ? 'NA' 
                  : $this->receptor->getFormadepago()),                             // Forma de pago Viene de CFormaPago
          'tipoDeComprobante' => 'I',                                               // Tipo de comprobante, viene de CTipoDeComprobante *
          'moneda' => 'MXN',                                                        // Moneda MXN. Viene de CMoneda *
          'tipoDeCambio' => '1',                                                    // Tipo de cambio. Requerido si Moneda es diferente de 'MXN'
          'observaciones' => array($this->comprobante->getObservaciones()),         // Observaciones
          'emisor' => array(
                'rfc' => strtoupper($this->emisor->getRfc()),                       // RFC
                'razonSocial' => strtoupper($this->emisor->getNombre()),            // Razón Social
                'regimenFiscal' => $this->emisor->getClave_regimen()),              // Régimen Fiscal, viene de CRegimenFiscal *
          'receptor' => array(
                'rfc' => strtoupper($this->receptor->getRfc()),                     // RFC
                'razonSocial' => $this->receptor->getNombre(),                      // Razon social
                'usoCFDI' => $this->getReceptor()->getUsoCFDI()),                   // Uso del CFDI, viene de CUsoCFDI
          'lugarExpedicion' => $this->emisor->getCodigo(),                          // Codigo Postal
          'conceptos' => $conceptos,                                                // Conceptos apmarados por el CFDI
          'impuestos' => array(
                'traslados' => $gtraslados,
                'retenciones' => $gretenciones,
                'TotalImpuestosTrasladados' => round($ITTotal, 2),
                'TotalImpuestosRetenidos' => round($IRTotal, 2)));
        return $cfdi;
    }//getCFDIJson

    private function __conceptosToString() {
        $conceptos = "[";
        $i = 0;
        foreach ($this->conceptos as $concepto) {
            $conceptos = $conceptos . ($i++==0 ? "" : ", ") . $concepto;
        }
        return $conceptos . "]";
    }//__conceptosToString

    public function __toString() {
        return "FacturaVO=comprobante=".$this->comprobante
                .", emisor=".$this->emisor
                .", receptor=".$this->receptor
                .", conceptos=".$this->__conceptosToString()."}";
    }//__toString

}//FacturaVO
