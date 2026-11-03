import React, { useEffect, useRef, useState } from 'react';
import { createPortal } from 'react-dom';
import MemberLink from '../MemberLink';

const ReactionSummary = ({ reactions = [], availableEmojis = [], onReaction, compact = false }) => {
	const [activeTooltip, setActiveTooltip] = useState(null);
	const hideTooltipTimeout = useRef(null);
	const emojis = onReaction
		? [...new Set([...availableEmojis, ...reactions.map(reaction => reaction.emoji)])]
		: reactions.map(reaction => reaction.emoji);
	const clearHideTooltipTimeout = () => window.clearTimeout(hideTooltipTimeout.current);
	const hideTooltip = () => {
		hideTooltipTimeout.current = window.setTimeout(() => setActiveTooltip(null), 150);
	};
	const showTooltip = (event, emoji, reaction) => {
		if (!reaction) {
			return;
		}

		clearHideTooltipTimeout();

		const { bottom, left, top, width } = event.currentTarget.getBoundingClientRect();
		const spaceBelow = window.innerHeight - bottom - 8;
		const spaceAbove = top - 8;
		const showAbove = spaceBelow < 260 && spaceAbove > spaceBelow;

		setActiveTooltip({
			emoji,
			reaction,
			left: Math.max(8, Math.min(left + width / 2 - 120, window.innerWidth - 248)),
			top: showAbove ? top - 4 : bottom + 4,
			maxHeight: Math.max(0, showAbove ? spaceAbove : spaceBelow),
			showAbove,
		});
	};

	useEffect(() => () => window.clearTimeout(hideTooltipTimeout.current), []);

	if (emojis.length === 0) {
		return null;
	}

	return (
		<div className={`flex flex-wrap items-center ${compact ? 'gap-1' : 'gap-2'}`} aria-label="Reactions">
			{emojis.map(emoji => {
				const reaction = reactions.find(item => item.emoji === emoji);
				const reactors = reaction?.reactors ?? [];
				const reactorNames = reactors.map(reactor => reactor.name).join(', ');
				const label = `${emoji} reaction${reaction ? `, ${reaction.count}` : ''}${
					reactorNames ? `, by ${reactorNames}` : ''
				}`;
				const tooltip =
					activeTooltip?.emoji === emoji && typeof document !== 'undefined'
						? createPortal(
								<div
									role="tooltip"
									className="fixed z-[1000] max-h-60 w-max max-w-[calc(100vw-1rem)] overflow-y-auto"
									style={{
										left: `${activeTooltip.left}px`,
										top: `${activeTooltip.top}px`,
										maxHeight: `${activeTooltip.maxHeight}px`,
										transform: activeTooltip.showAbove ? 'translateY(-100%)' : undefined,
									}}
									onMouseEnter={clearHideTooltipTimeout}
									onMouseLeave={hideTooltip}
								>
									<div className="rounded-md border border-gray-200 bg-white p-2 shadow-lg">
										{activeTooltip.reaction.reactors?.length > 0 ? (
											<div className="flex flex-col gap-2">
												{activeTooltip.reaction.reactors.map(reactor => (
													<div
														key={reactor.id}
														className="flex items-center gap-2 break-words text-xs text-gray-700"
													>
														<img
															src={reactor.avatar_url}
															alt=""
															className="h-6 w-6 shrink-0 rounded-full object-cover"
														/>
														<MemberLink
															membershipId={reactor.id}
															className="break-words text-xs"
														>
															{reactor.name}
														</MemberLink>
													</div>
												))}
											</div>
										) : (
											<span className="text-xs text-gray-500">No member details available</span>
										)}
									</div>
								</div>,
								document.body
						  )
						: null;

				return (
					<React.Fragment key={emoji}>
						{onReaction ? (
							<button
								type="button"
								aria-pressed={Boolean(reaction?.reacted)}
								aria-label={`${emoji} reaction`}
								title={reactorNames || undefined}
								className={`inline-flex items-center gap-1 rounded-full border ${
									compact ? 'px-1.5 py-0.5 text-xs' : 'px-3 py-1.5 text-sm'
								} font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 ${
									reaction?.reacted
										? 'border-purple-400 bg-purple-50 text-purple-800'
										: 'border-gray-200 bg-white text-gray-700 hover:border-purple-300 hover:bg-gray-50'
								}`}
								onMouseEnter={event => showTooltip(event, emoji, reaction)}
								onMouseLeave={hideTooltip}
								onFocus={event => showTooltip(event, emoji, reaction)}
								onBlur={hideTooltip}
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
								className={`inline-flex items-center gap-1 rounded-full border ${
									compact ? 'px-1.5 py-0.5 text-xs' : 'px-2.5 py-1 text-sm'
								} font-medium ${
									reaction?.reacted
										? 'border-purple-300 bg-purple-50 text-purple-800'
										: 'border-gray-200 bg-white text-gray-700'
								}`}
								onMouseEnter={event => showTooltip(event, emoji, reaction)}
								onMouseLeave={hideTooltip}
								onFocus={event => showTooltip(event, emoji, reaction)}
								onBlur={hideTooltip}
							>
								{emoji}
								{reaction && <span>{reaction.count}</span>}
							</span>
						)}
						{tooltip}
					</React.Fragment>
				);
			})}
		</div>
	);
};

export default ReactionSummary;
