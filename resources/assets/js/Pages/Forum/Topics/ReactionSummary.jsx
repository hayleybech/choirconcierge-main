import React from 'react';

const ReactionSummary = ({ reactions = [], availableEmojis = [], onReaction }) => {
	const emojis = onReaction
		? [...new Set([...availableEmojis, ...reactions.map(reaction => reaction.emoji)])]
		: reactions.map(reaction => reaction.emoji);

	if (emojis.length === 0) {
		return null;
	}

	return (
		<div className="flex flex-wrap items-center gap-2" aria-label="Reactions">
			{emojis.map(emoji => {
				const reaction = reactions.find(item => item.emoji === emoji);
				const reactors = reaction?.reactors ?? [];
				const reactorNames = reactors.map(reactor => reactor.name).join(', ');
				const label = `${emoji} reaction${reaction ? `, ${reaction.count}` : ''}${reactorNames ? `, by ${reactorNames}` : ''}`;

				return (
					<span key={emoji} className="group relative inline-flex">
						{onReaction ? (
							<button
								type="button"
								aria-pressed={Boolean(reaction?.reacted)}
								aria-label={`${emoji} reaction`}
								title={reactorNames || undefined}
								className={`inline-flex items-center gap-1 rounded-full border px-3 py-1.5 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 ${
									reaction?.reacted
										? 'border-purple-400 bg-purple-50 text-purple-800'
										: 'border-gray-200 bg-white text-gray-700 hover:border-purple-300 hover:bg-gray-50'
								}`}
								onClick={() => onReaction(emoji)}
							>
								{emoji}
								{reaction && <span>{reaction.count}</span>}
							</button>
						) : (
							<span
								tabIndex={0}
								role="img"
								aria-label={label}
								title={reactorNames || undefined}
								className={`inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-sm font-medium ${
									reaction?.reacted
										? 'border-purple-300 bg-purple-50 text-purple-800'
										: 'border-gray-200 bg-white text-gray-700'
								}`}
							>
								{emoji}
								{reaction && <span>{reaction.count}</span>}
							</span>
						)}
						{reaction && (
							<span className="invisible absolute left-1/2 top-full z-20 w-max max-w-60 -translate-x-1/2 pt-1 opacity-0 transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
								<span role="tooltip" className="block max-h-60 overflow-y-auto rounded-md border border-gray-200 bg-white p-2 shadow-lg">
									{reactors.length > 0 ? (
										<span className="flex flex-col gap-2">
											{reactors.map(reactor => (
												<span key={reactor.id} className="flex items-center gap-2 whitespace-nowrap text-xs text-gray-700">
													<img src={reactor.avatar_url} alt="" className="h-6 w-6 rounded-full object-cover" />
													<span>{reactor.name}</span>
												</span>
											))}
										</span>
									) : (
										<span className="text-xs text-gray-500">No member details available</span>
									)}
								</span>
							</span>
						)}
					</span>
				);
			})}
		</div>
	);
};

export default ReactionSummary;