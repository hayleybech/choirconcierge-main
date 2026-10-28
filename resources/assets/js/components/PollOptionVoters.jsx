import React, { useState } from 'react';
import Dialog from './Dialog';

const PollOptionVoters = ({ option }) => {
	const [isOpen, setIsOpen] = useState(false);
	const voters = option.voters || [];
	const previewVoters = voters.slice(0, 3);
	const hiddenVoterCount = voters.length - previewVoters.length;

	if (voters.length === 0) {
		return null;
	}

	return (
		<>
			<div className="flex shrink-0 items-center -space-x-3">
				{previewVoters.map(voter => (
					<img
						key={voter.id}
						className="h-8 w-8 shrink-0 cursor-default rounded-full border-2 border-white object-cover"
						src={voter.avatar_url}
						alt={voter.name}
						title={voter.name}
					/>
				))}
				{hiddenVoterCount > 0 && (
					<button
						type="button"
						className="flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full border-2 border-white bg-gray-200 text-xs font-semibold text-gray-700"
						aria-label={`Show ${hiddenVoterCount} more voters for ${option.label}`}
						onClick={event => {
							event.stopPropagation();
							setIsOpen(true);
						}}
					>
						+{hiddenVoterCount}
					</button>
				)}
			</div>
			<Dialog
				title={`Voters for ${option.label}`}
				isOpen={isOpen}
				setIsOpen={setIsOpen}
				icon={null}
			>
				<ul className="space-y-3 text-left">
					{voters.map(voter => (
						<li key={voter.id} className="flex items-center gap-3">
							<img className="h-8 w-8 rounded-full object-cover" src={voter.avatar_url} alt="" />
							<span className="font-medium text-gray-900">{voter.name}</span>
						</li>
					))}
				</ul>
			</Dialog>
		</>
	);
};

export default PollOptionVoters;