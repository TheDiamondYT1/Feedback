<?php

namespace TheDiamondYT\Feedback;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase implements Listener {
	/** @var array */
	private $feedback = [];
	
	/** @var Config */
	private $file;

	public function onEnable() {
		$this->saveDefaultConfig();
		$this->file = new Config($this->getDataFolder()  . "/feedback.json", Config::JSON);
		$this->feedback = ($this->file)->getAll();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getCommandMap()->register("feedback", new FeedbackCommand($this));
	}
	
	/**
	 * @param PlayerJoinEvent $ev
	 */
	public function onPlayerJoin(PlayerJoinEvent $ev) {
		$player = $ev->getPlayer();
		if($player->hasPermission("feedback.read")) {
			$player->sendMessage($this->getConfig()->get("header"));
		}
	}
	
	/**
	 * Add new feedback.
	 *
	 * @param string $author
	 * @param string $text
	 */
	public function addFeedback(string $author, string $text) { 
		$this->feedback[] = [
			"author" => $author,
			"text" => $text,
			"createdAt" => date("F jS, Y h:i:s", strtotime("now"))
		];
		$this->file->setAll($this->feedback);
		$this->file->save();
	}
	
	/**
	 * Returns all the feedback.
	 *
	 * @return array
	 */
	public function getFeedback(): array {
		return $this->feedback;
	}
}
