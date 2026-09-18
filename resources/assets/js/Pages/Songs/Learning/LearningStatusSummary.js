import React from 'react';
import Icon from "../../../components/Icon";
import LearningStatus from "../../../LearningStatus";

const LearningStatusSummary = ({ singers }) => (
	<div className="bg-white border-b border-gray-200 grid grid-cols-3">
		{['performance-ready', 'assessment-ready', 'not-started'].map(slug => {
			const status = new LearningStatus(slug);
			const count = singers.filter(singer => singer.learning.status === slug).length;

			return (
				<div className="text-center flex flex-col items-center justify-center py-2 px-2 lg:py-4 border-gray-100" key={slug}>
					<div className={`flex flex-col sm:flex-row items-center gap-2 font-bold ${status.textColour} mb-1 text-sm md:text-base`}>
						<Icon icon={status.icon} />
						{status.title}
					</div>
					<span className="text-xl md:text-2xl font-bold text-gray-900">{count}</span>
				</div>
			);
		})}
	</div>
);

export default LearningStatusSummary;
