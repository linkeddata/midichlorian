# Midi-Chlorian

This is a sample app to demonstrate how serverside code can be used to alleviate the JS-requirement constraint for the retrieval of public resources on [Solid](https://github.com/solid) servers. Basically the server tries to fetch the resource and if it can't get it because there's access control set, the clientside JS tries to authenticate you then fetch it. So JS is required <em>only</em> for non-public resources, which is better than being required for everything.

[apps.rhiaro.co.uk/midichlorian](https://apps.rhiaro.co.uk/midichlorian)

It's called Midi-Chlorian because I was thinking for names around *symbiosis* and I vividly remember when I learnt the word *symbiotic* from Star Wars: The Phantom Menace when I was 9.