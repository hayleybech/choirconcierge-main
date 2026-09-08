import { shouldShowSheetMusicColumn } from './showSheetMusic';

describe('shouldShowSheetMusicColumn', () => {
	it('hides the sheet music column on compact desktop screens', () => {
		expect(shouldShowSheetMusicColumn({ isDesktop: true, isCompactDesktop: true, isFullscreen: false })).toBe(
			false
		);
	});

	it('shows the sheet music column on large desktop screens when not fullscreen', () => {
		expect(shouldShowSheetMusicColumn({ isDesktop: true, isCompactDesktop: false, isFullscreen: false })).toBe(
			true
		);
	});
});
