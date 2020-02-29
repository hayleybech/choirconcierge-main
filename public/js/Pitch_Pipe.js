class Pitch_Pipe {
    constructor(selector, key) {
        this.el = document.querySelector(selector);
        this.el_key = this.el.querySelector('.key');
        this.synth = new Tone.Synth().toMaster();

        this.DEFAULT_OCTAVE = 4;
        this.DEFAULT_DURATION = '1m';

        this.setKey(key);
        this.bindAll();
    }

    play() {
        this.synth.triggerAttackRelease( this.pitch, this.DEFAULT_DURATION);
    }

    bindAll() {
        let self = this;
        this.el.addEventListener('click', function(){
            self.play();
        })
    }

    setKey(key) {
        this.key = key;
        this.el_key.textContent = key;
        this.pitch = this.key + this.DEFAULT_OCTAVE.toString();
    }
}