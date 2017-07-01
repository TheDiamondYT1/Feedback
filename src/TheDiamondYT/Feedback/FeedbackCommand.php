<?php

namespace TheDiamondYT\Feedback;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat as TF;

class FeedbackCommand extends Command implements PluginIdentifiableCommand {
	/** @var Loader */
	private $loader;

	public function __construct(Loader $loader) {
		parent::__construct("feedback", "Give feedback for the server.", "/feedback <text>");
		$this->loader = $loader;
		$this->setPermission("feedback.give");
	}
	
	public function execute(CommandSender $sender, $label, array $args) {
		if(!$this->testPermission($sender)) {
			return false;
		}
		if(!$sender instanceof Player) {
			$sender->sendMessage(TF::RED . "Silly console, why would you give feedback to yourself?");
			return true;
		}
		if(empty($args)) {
			$sender->sendMessage(TF::RED . $this->getUsage());
			return true;
		}
		if($args[0] === "read") {
			$page = 1;
			if(isset($args[1]) and is_numeric($args[1])) {
				$page = (int) array_shift($args);
				if($page <= 0) $page = 1;
			}
			$feedbacks = [];
			foreach($this->getLoader()->getFeedback() as $feedback) {
				$feedbacks[] = $feedback;
			}
			ksort($feedbacks, SORT_NATURAL | SORT_FLAG_CASE);
			$feedbacks = array_chunk($feedbacks, 7);
			$page = (int) min(count($feedbacks), $page);
			
			$sender->sendMessage($this->getLoader()->getConfig()->get("header"));
			foreach($feedbacks[$page -1] as $feedback) {
				$sender->sendMessage(TF::GREEN . $feedback["author"] . TF::AQUA . " " . $feedback["text"]);
			}
			return true;
		}
		$this->getLoader()->addFeedback($sender->getName(), implode(" ", $args));
		$sender->sendMessage(TF::GREEN  . "Feedback successfully sent.");
		
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($player->hasPermission("feedback.read")) {
				$player->sendMessage(TF::GOLD . "New feedback from " . $sender->getName() . "!");
				$player->sendMessage(TF::GOLD . "Read it with /feedback read");
			}
		}
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
	
	public function getPlugin(): Loader {
		return $this->loader;
	}
}
