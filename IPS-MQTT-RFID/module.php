<?php

declare(strict_types=1);

class IPS_MQTT_RFID extends IPSModule
{
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->ConnectParent('{EE0D345A-CF31-428A-A613-33CE98E752DD}');

        $this->RegisterPropertyString('MQTTTopic', '');
        $this->RegisterVariableString('RFID_UID', 'UID', '');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->ConnectParent('{EE0D345A-CF31-428A-A613-33CE98E752DD}');
        //Setze Filter für ReceiveData
        $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
        $this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');
    }

    public function ReceiveData($JSONString)
    {
        $this->SendDebug('JSON', $JSONString, 0);
        if (!empty($this->ReadPropertyString('MQTTTopic'))) {
            $data = json_decode($JSONString);
            // Buffer decodieren und in eine Variable schreiben
            $Buffer = json_decode($data->Buffer);
            $this->SendDebug('MQTT Topic', $Buffer->TOPIC, 0);

            if (property_exists($Buffer, 'TOPIC')) {
                if (fnmatch('*uid*', $Buffer->TOPIC)) {
                    $this->SendDebug('Topic', $Buffer->TOPIC, 0);
                    $this->SendDebug('Msg', $Buffer->MSG, 0);
                    SetValue($this->GetIDForIdent('RFID_UID'), $Buffer->MSG);
                }
            }
        }
    }
}
