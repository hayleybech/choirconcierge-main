import {useCallback, useState} from "react";
import {Sampler, Synth} from "tone";
import useCookie from 'react-use-cookie';

export const useInstrument = () => {
    const [savedInstrument, storeInstrument] = useCookie('pitch-instrument', 'sine');
    const [instrument, _setInstrument] = useState(() => getInstrument(savedInstrument));
    const setInstrument = useCallback(instrumentName => {
        const _instr = getInstrument(instrumentName);
        _setInstrument(_instr);
        storeInstrument(_instr.name);
    }, []);

    return [instrument, setInstrument];
};

const getInstrument = (instrumentName) => {
    if (instrumentName === 'sine') {
        const synth = new Synth().toDestination();
        synth.envelope.release = 0.3;
        synth.envelope.sustain = 0.1;
        synth.volume.value = 20;

        return {
            name: 'sine',
            instrument: synth,
        };
    }
    if (instrumentName === 'piano') {
        const piano = new Sampler({
            urls: {
                'C4': '39187__jobro__piano-ff-040.wav',
                'C#4': '39188__jobro__piano-ff-041.wav',
                'D4': '39189__jobro__piano-ff-042.wav',
                'D#4': '39190__jobro__piano-ff-043.wav',
                'E4': '39191__jobro__piano-ff-044.wav',
                'F4': '39193__jobro__piano-ff-045.wav',
                'F#4': '39194__jobro__piano-ff-046.wav',
                'G4': '39195__jobro__piano-ff-047.wav',
                'G#4': '39196__jobro__piano-ff-048.wav',
                'A4': '39197__jobro__piano-ff-049.wav',
                'A#4': '39198__jobro__piano-ff-050.wav',
                'B4': '39199__jobro__piano-ff-051.wav',
                'C5': '39200__jobro__piano-ff-052.wav',
            },
            baseUrl: '/audio/piano/',
        }).toDestination();
        piano.volume.value = 5;

        return {
            name: 'piano',
            instrument: piano,
        };
    }
    if(instrumentName === 'pipe') {

        const pipe = new Sampler({
            // yes the octave here is a lie. sue me.
            urls: {
                'C4': '362156__inspectorj__guitar-pitch-pipe-c3.wav',
                'C#4': '362158__inspectorj__guitar-pitch-pipe-c-sharp3.wav',
                'D4': '362160__inspectorj__guitar-pitch-pipe-d3.wav',
                'D#4': '362172__inspectorj__guitar-pitch-pipe-e-flat3.wav',
                'E4': '362177__inspectorj__guitar-pitch-pipe-e3.wav',
                'F4': '362164__inspectorj__guitar-pitch-pipe-f3.wav',
                'F#4': '362174__inspectorj__guitar-pitch-pipe-f-sharp3.wav',
                'G4': '362162__inspectorj__guitar-pitch-pipe-g3.wav',
                'G#4': '362154__inspectorj__guitar-pitch-pipe-a-flat3.wav',
                'A4': '362153__inspectorj__guitar-pitch-pipe-a3.wav',
                'A#4': '362152__inspectorj__guitar-pitch-pipe-b-flat3.wav',
                'B4': '362151__inspectorj__guitar-pitch-pipe-b3.wav',
                'C5': '362155__inspectorj__guitar-pitch-pipe-c4.wav',
            },
            baseUrl: '/audio/pipe/',
        }).toDestination();
        pipe.volume.value = 5;

        return {
            name: 'pipe',
            instrument: pipe,
        };
    }
    if(instrumentName === 'cat') {

        const cat = new Sampler({
            urls: {
                'C4': '336054__ipaghost__ipa_meowvoice_13_c4.wav',
                'C#4': '336053__ipaghost__ipa_meowvoice_14_c4.wav',
                'D4': '336060__ipaghost__ipa_meowvoice_15_d4.wav',
                'D#4': '336059__ipaghost__ipa_meowvoice_16_d4.wav',
                'E4': '336058__ipaghost__ipa_meowvoice_17_e4.wav',
                'F4': '336057__ipaghost__ipa_meowvoice_18_f4.wav',
                'F#4': '336062__ipaghost__ipa_meowvoice_19_f4.wav',
                'G4': '336061__ipaghost__ipa_meowvoice_20_g4.wav',
                'G#4': '336045__ipaghost__ipa_meowvoice_21_g4.wav',
                'A4': '336046__ipaghost__ipa_meowvoice_22_a4.wav',
                'A#4': '336043__ipaghost__ipa_meowvoice_23_a4.wav',
                'B4': '336044__ipaghost__ipa_meowvoice_24_b4.wav',
                'C5': '336049__ipaghost__ipa_meowvoice_25_c5.wav',
            },
            baseUrl: '/audio/cat/',
        }).toDestination();
        cat.volume.value = 5;

        return {
            name: 'cat',
            instrument: cat,
        };
    }
    if(instrumentName === 'wilhelm') {
        const wilhelm = new Sampler({
            urls: {
                'F4': '64940__syna-max__wilhelm_scream.wav',
            },
            baseUrl: '/audio/wilhelm/',
        }).toDestination();
        wilhelm.volume.value = 5;

        return {
            name: 'wilhelm',
            instrument: wilhelm,
        };
    }
    if(instrumentName === 'sleigh') {
        const sleigh = new Sampler({
            urls: {
                'E4': '369506__selector__sleigh-bells-hit.wav',
            },
            baseUrl: '/audio/sleigh/',
        }).toDestination();
        sleigh.volume.value = 5;

        return {
            name: 'sleigh',
            instrument: sleigh,
        };
    }
}