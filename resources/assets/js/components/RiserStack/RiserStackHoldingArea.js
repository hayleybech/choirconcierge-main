import React from 'react';

const RiserStackHoldingArea = ({ voiceParts }) => (
	<div className="h-full overflow-y-auto" aria-label="Holding Area">
		{voiceParts.map((voicePart) =>
			<div key={voicePart.id} className="relative">
				<div className="z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
					<h3 style={{ color: voicePart.colour }}>{voicePart.title}</h3>
				</div>
				<ul role="list" className="relative z-0 divide-y divide-gray-200">
					{voicePart.singers.map((singer) => (
						<li key={singer.id} className="bg-white">
							<div className="relative px-6 py-5 flex items-center space-x-3 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
								<div className="flex-shrink-0">
									<img className="h-8 w-8 rounded-lg" src={singer.user.avatar_url} alt={singer.user.name} />
								</div>
								<div className="flex items-center justify-between px-4">
									<span className="text-sm font-medium truncate">{singer.user.name}</span>
								</div>
							</div>
						</li>
					))}
				</ul>
			</div>
		)}
	</div>
);

export default RiserStackHoldingArea;