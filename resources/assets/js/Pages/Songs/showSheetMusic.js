export const shouldShowSheetMusicColumn = ({ isDesktop, isCompactDesktop, isFullscreen }) =>
	isDesktop && !isCompactDesktop && !isFullscreen;
