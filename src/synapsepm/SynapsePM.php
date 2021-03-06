<?php
declare(strict_types=1);

namespace synapsepm;

use pocketmine\plugin\PluginBase;


class SynapsePM extends PluginBase {
	/** @var Synapse[] */
	private $synapses = [];
	/** @var bool */
	private $useLoadingScreen;

	public function onEnable() {
		$this->saveDefaultConfig();
		$this->reloadConfig();

		if (!$this->getConfig()->get('enabled')) {
			$this->setEnabled(false);
			return;
		}

		if ($this->getConfig()->get('disable-rak')) {
			$this->getServer()->getPluginManager()->registerEvents(new DisableRakListener(), $this);
		}

		foreach ($this->getConfig()->get('synapses') as $synapseConfig) {
			if ($synapseConfig['enabled']) {
				$this->addSynapse(new Synapse($this, $synapseConfig));
			}
		}




		$this->useLoadingScreen = (bool)$this->getConfig()->get('loadingScreen', true);
	}

	public function onDisable() {
		foreach ($this->synapses as $synapse) {
			$synapse->shutdown();
		}
	}

	/**
	 * Add the synapse to the synapses list
	 *
	 * @param Synapse $synapse
	 */
	public function addSynapse(Synapse $synapse) {
		$this->synapses[spl_object_hash($synapse)] = $synapse;
	}

	/**
	 * Remove the synapse from the synapses list
	 *
	 * @param Synapse $synapse
	 */
	public function removeSynapse(Synapse $synapse) {
		unset($this->synapses[spl_object_hash($synapse)]);
	}

	/**
	 * Return array of the synapses
	 * @return Synapse[]
	 */
	public function getSynapses() : array {
		return $this->synapses;
	}

	/**
	 * @return boolean
	 */
	public function isUseLoadingScreen() : bool {
		return $this->useLoadingScreen;
	}
}