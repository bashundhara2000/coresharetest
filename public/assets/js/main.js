

/*-----------------------------------------------------------------------------
Structure to store the private key and Public key of users
-----------------------------------------------------------------------------*/
var key_Struct =
{
	sk_1 : "",
	sk_2 : "",
	pk_1 : "",
	pk_2 : ""
}
/*-----------------------------------------------------------------------------
Structure to store the private key of users
-----------------------------------------------------------------------------*/
var secretkey_Struct =
{
	sk_1 : "",
	sk_2 : ""
}
/*-----------------------------------------------------------------------------
Structure to store the Public key of users
-----------------------------------------------------------------------------*/
var publickey_Struct =
{
	pk_1 : "",
	pk_2 : ""
}
/*-----------------------------------------------------------------------------
Structure to store the Re-encryption key of users
-----------------------------------------------------------------------------*/
var rekey_Struct =
{
	RKi_j : "",
	V : "",
	W : ""
}
/*-----------------------------------------------------------------------------
Structure to store first level Ciphertext (re-encryptable) 
-----------------------------------------------------------------------------*/
var ct1_Struct =
{
	level : "",
	D : "",
	E : "",
	F : "",
	s : ""
}
/*-----------------------------------------------------------------------------
Structure to store first level Ciphertext (non re-encryptable) 
-----------------------------------------------------------------------------*/
var ct2_Struct =
{
	level : "",
	E1 : "",
	F : "",
	V : "",
	W : ""
}
/*-----------------------------------------------------------------------------
Structure PRE stores the public components (p, q, g) which are used by all the 
algorithms. The members implement the various algorithms of the PRE and all 
related functions.
-> q divides p-1
-> g is the generator
-----------------------------------------------------------------------------*/
var PRE =
{
	q : "938A7EC1C13A752CCBF0BAE0510919361CAD0726729ABDCC616F6D5395EE7D59",
	p : "BDC1C698F4A9DB64CF009F9ECCEE7BD5351E518DAE8669116280DADD987F1A3403BFEF74CF6F63523BF7269039B38B7A039A61A01AB180944D0F5B0F8C95AD14B67459CE22D762FEFD2402615A9520CFA6A74D23A302E1E7A45755ADDA11BD283896E345AED2AFD5747F6935A85BC5CBD946291B4983323B42CCB3255F30F241EC4A83EF0E5481EF7D792E218F01237DCAC502F2A58898C60767C1783EAA15FBDDD83F96FC7F7D4B540967106B9F4443D72A1BE814E106D3F8EED33BC726808FBDF951BA1EC46D422B294B661E09E89ED424E6E90DCA6E29C520DE0ED4C0670BF3653BEECF1B7FE63A135BBF7630C66CA6CFAFF572E43E04A1CB00C60804626F",
	g : "1790D3957529B1FC9E945B245B87ED09D48D9141176357666512EE57331417D48B3DACBEE9021056E2E3206C5A32AAC42E35C51DA686CAE494BC9C17F5D52DED7FBC9F28529F309000AC357DE5487900FD57F0FA09906F611B720E06EEC00F6210EED3675B47D5FE0CB5E8B8C3905D10613FA5BDE09A510836274384720864AC16E0FEB4F9CA3AEA1F675A66E2FDC207D7F41C2E2CEDD5068C5EE5C5F2C40208C462F2EBC1BA8523613A352D3CD61D94AB075C40FC5B2879F030AC276CB6C7D6028DFC525D8C4E722380F7BB7626B944C79DFA70A1BC283E67A144F2326ABFCC4EB5E91A104427AFEFF29D64D096006E656A859C0EE812CEDAF0DE7F8B083CA9",
	
	
	/*-----------------------------------------------------------------------------
	Function to convert bitarray to bn
	-----------------------------------------------------------------------------*/
	BITbn_to_HEXbn : function(inputBITbn)
	{
		var temp = sjcl.codec.hex.fromBits(inputBITbn);
		outputHEXbn = new sjcl.bn(temp);
		
		return outputHEXbn;
	},
	/*-----------------------------------------------------------------------------
	Function takes a string as input computes the SHA256 hash and outputs the bn
	-----------------------------------------------------------------------------*/	
	SHA256_str2bn : function(inputstr)
	{
		var temp = sjcl.hash.sha256.hash(inputstr);
		var outputbn = this.BITbn_to_HEXbn(temp);
		
		return outputbn;
	},	
	/*-----------------------------------------------------------------------------
	Function to perform XOR operation. (CAUTION: Custom XOR function, reduces the 
	first input	to the size of the second input)
	-----------------------------------------------------------------------------*/
	xorAll : function(inpa,inpb)
	{
		var bitlena, bitlenb, ai, bi;

		bitlena = inpa.bitLength();
		bitlenb = inpb.bitLength();
		
		ai_bits = inpa.toBits();
		bi_bits = inpb.toBits();

		if (bitlena > bitlenb)
		{
			bitlen = bitlenb;
		}
		else if (bitlenb > bitlena)
		{
			bitlen = bitlenb;
		}
		else
		{
			bitlen = bitlena;
		}
		ai_bits = sjcl.bitArray.clamp(ai_bits, bitlen);
		bi_bits = sjcl.bitArray.clamp(bi_bits, bitlen);
		var output = [];

		for (i = bitlen - 1; i >= 0; i-=1) 
		{
			output[i] = ai_bits[i] ^ bi_bits[i];
		}
		output = sjcl.bitArray.clamp(output, bitlen);
		
		return this.BITbn_to_HEXbn(output);
	},
	/*-----------------------------------------------------------------------------
	String to hex converter
	-----------------------------------------------------------------------------*/			
	toHex :function(str) 
	{
		var hex = '';
		for(var i=0;i<str.length;i++) {
			hex += ''+str.charCodeAt(i).toString(16);
		}
		return hex;
	},
 	/*-----------------------------------------------------------------------------
	Hex to string converter
	-----------------------------------------------------------------------------*/		
    hex2a :function(hexx) 
	{
		var hex = hexx.toString();//force conversion
		var str = '';
		for (var i = 0; i < hex.length; i += 2)
			str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
		return str;
	},
	/*-----------------------------------------------------------------------------
	HashBlowup function. 
	Input is string. output is a blown up hash output. 
	CAUTION: To be customized later.
	-----------------------------------------------------------------------------*/	
	HashBlowup : function(inp)
	{
		var t511 = inp.add(1);
		var t521 = this.SHA256_str2bn(t511.toString().replace('0x',''));
		var t512 = t521.add(1);
		var t522 = this.SHA256_str2bn(t512.toString().replace('0x',''));
		var t513 = t522.add(1);
		var t523 = this.SHA256_str2bn(t513.toString().replace('0x',''));
		var t514 = t523.add(1);
		var t524 = this.SHA256_str2bn(t514.toString().replace('0x',''));
		var t514 = t524.add(1);

		var inpa = t521.toString().replace('0x','') + t522.toString().replace('0x','') + 
					t523.toString().replace('0x','') + t524.toString().replace('0x','');
		inpa = inpa.replace('0x',"");
		inpa = new sjcl.bn(inpa);

		return inpa;
	},
	/*-----------------------------------------------------------------------------
	MSK generation function
	Output - MSK as string
	-----------------------------------------------------------------------------*/	
	Gen_MSK : function()
	{
		var MSK = sjcl.bn.random(PRE.q);
		var MSK = new sjcl.bn(MSK);
		
		return MSK.toString().replace('0x','');
	},
	/*-----------------------------------------------------------------------------
	Encrypt function - Encrypt MSK to obtain EMSK function	
	Input - password and MSK as strings
	Output - EMSK, the encrypted MSK 
	-----------------------------------------------------------------------------*/	
	Encrypt_MSK : function(password, MSK)
	{
		EMSK = sjcl.encrypt(password, MSK, {ks:256},{mode : "ccm"});
				
		return EMSK;
	},
	/*-----------------------------------------------------------------------------
	Decrypt function - Decrypt EMSK to obtain MSK
	Input - password as strings and EMSK as a ciphertext array
	Output - MSK as string
	-----------------------------------------------------------------------------*/	
	Decrypt_MSK : function(password, EMSK)
	{
		try
		{		
			MSK = sjcl.decrypt(password, EMSK); 
		}
		catch(error)
		{
			console.log("Error: " + error);
			return;
		}
		return MSK;
	},
	/*-----------------------------------------------------------------------------
	Derive File encryption key from MSK
	Output - MSK as string
	Output: [nonce, FEK] as string array
	-----------------------------------------------------------------------------*/	
	Derive_FEK : function(MSK)
	{
		var nonce = sjcl.bn.random(PRE.q);
		var nonce = new sjcl.bn(nonce);
		var key = MSK;
		var hmac = new sjcl.misc.hmac(key, sjcl.hash.sha256);
		FEK = PRE.BITbn_to_HEXbn(hmac.encrypt(nonce.toString().replace('0x','')));	
		
		return [nonce.toString().replace('0x',''), FEK];
	},
	/*-----------------------------------------------------------------------------
	Function to generate safe prime (Now it is hardcoded)
	-----------------------------------------------------------------------------*/
	Gen_Safeprimes : function()
	{
		this.p = new sjcl.bn("BDC1C698F4A9DB64CF009F9ECCEE7BD5351E518DAE8669116280DADD987F1A3403BFEF74CF6F63523BF7269039B38B7A039A61A01AB180944D0F5B0F8C95AD14B67459CE22D762FEFD2402615A9520CFA6A74D23A302E1E7A45755ADDA11BD283896E345AED2AFD5747F6935A85BC5CBD946291B4983323B42CCB3255F30F241EC4A83EF0E5481EF7D792E218F01237DCAC502F2A58898C60767C1783EAA15FBDDD83F96FC7F7D4B540967106B9F4443D72A1BE814E106D3F8EED33BC726808FBDF951BA1EC46D422B294B661E09E89ED424E6E90DCA6E29C520DE0ED4C0670BF3653BEECF1B7FE63A135BBF7630C66CA6CFAFF572E43E04A1CB00C60804626F");
		this.q = new sjcl.bn("938A7EC1C13A752CCBF0BAE0510919361CAD0726729ABDCC616F6D5395EE7D59");
		this.g = new sjcl.bn("1790D3957529B1FC9E945B245B87ED09D48D9141176357666512EE57331417D48B3DACBEE9021056E2E3206C5A32AAC42E35C51DA686CAE494BC9C17F5D52DED7FBC9F28529F309000AC357DE5487900FD57F0FA09906F611B720E06EEC00F6210EED3675B47D5FE0CB5E8B8C3905D10613FA5BDE09A510836274384720864AC16E0FEB4F9CA3AEA1F675A66E2FDC207D7F41C2E2CEDD5068C5EE5C5F2C40208C462F2EBC1BA8523613A352D3CD61D94AB075C40FC5B2879F030AC276CB6C7D6028DFC525D8C4E722380F7BB7626B944C79DFA70A1BC283E67A144F2326ABFCC4EB5E91A104427AFEFF29D64D096006E656A859C0EE812CEDAF0DE7F8B083CA9");
	},
	/*-----------------------------------------------------------------------------
	KeyGen function. It generates the secret key and public key of the user.
	Returns a key_Struct object.
	-----------------------------------------------------------------------------*/
	KeyGen : function(MSK)
	{
		var g = new sjcl.bn(this.g);

		var key = MSK;
		var hmac = new sjcl.misc.hmac(key, sjcl.hash.sha256);
		sk_1 = PRE.BITbn_to_HEXbn(hmac.encrypt("SK1"));
		sk_2 = PRE.BITbn_to_HEXbn(hmac.encrypt("SK2"));
			
		var pk_1 = g.powermod(sk_1, this.p);
		var pk_2 = g.powermod(sk_2, this.p);
		var key = key_Struct;
		key.sk_1 = sk_1;
		key.sk_2 = sk_2;
		key.pk_1 = pk_1;
		key.pk_2 = pk_2;
		
		return key;
	},
	/*-----------------------------------------------------------------------------
	ExtractSecKey function. Input is a key_Struct object. 
	Returns a secretkey_Struct object which has only the secret keys of the user.
	-----------------------------------------------------------------------------*/
	ExtractSecKey : function(Keyobj)
	{
		var secKeyobj = secretkey_Struct;
		secKeyobj.sk_1 = Keyobj.sk_1;
		secKeyobj.sk_2 = Keyobj.sk_2;
		
		return secKeyobj;
	},
	/*-----------------------------------------------------------------------------
	ExtractPubKey function. Input is a key_Struct object. 
	Returns a publickey_Struct object which has only the secret keys of the user.
	-----------------------------------------------------------------------------*/	
	ExtractPubKey : function(Keyobj)
	{
		var pubKeyobj = publickey_Struct;
		pubKeyobj.pk_1 = Keyobj.pk_1;
		pubKeyobj.pk_2 = Keyobj.pk_2;

		return pubKeyobj;
	},
	/*-----------------------------------------------------------------------------
	Convert Public key object to string.
	-----------------------------------------------------------------------------*/	
	Obj2StrPubKey : function(pubKeyobj)
	{
		pk_1 = pubKeyobj.pk_1.toString().replace('0x','');
		pk_2 = pubKeyobj.pk_2.toString().replace('0x','');

		return [pk_1, pk_2];
	},	
	/*-----------------------------------------------------------------------------
	Convert Public key string to object.
	-----------------------------------------------------------------------------*/	
	Str2ObjPubKey : function(pk_1, pk_2)
	{
		var pubKeyobj = publickey_Struct;
		pubKeyobj.pk_1 = new sjcl.bn(pk_1);
		pubKeyobj.pk_2 = new sjcl.bn(pk_2);

		return pubKeyobj;
	},		
	/*-----------------------------------------------------------------------------
	ReKeyGen function. 
	Input is secret_Keyobj of delegator i, public_Keyobj of the delegator i and 
	the public_Keyobj of the delegatee j. 
	Returns a rekey_Struct object which us used to re-encrypt from i to j.
	-----------------------------------------------------------------------------*/		
	ReKeyGen : function(secret_Keyobj_i, public_Keyobj_i, public_Keyobj_j)
	{
		var rekey_Keyobj = rekey_Struct;
		ski_1 = secret_Keyobj_i.sk_1;
		ski_2 = secret_Keyobj_i.sk_2;
		pki_1 = public_Keyobj_i.pk_1;
		pki_2 = public_Keyobj_i.pk_2;
		pkj_1 = public_Keyobj_j.pk_1;
		pkj_2 = public_Keyobj_j.pk_2;

		var h = sjcl.bn.random(this.q);
		var pi = sjcl.bn.random(this.q);
		var h = new sjcl.bn(h);		
		var pi = new sjcl.bn(pi);
		
		var htemp = h;
		h = h.toString().replace('0x','');	
		pi = pi.toString().replace('0x','');
		
		var v = this.SHA256_str2bn(h + pi); 
		var V = pkj_2.powermod(v, this.p);
		
		var t12 = this.g.powermod(v, this.p);
		inpa = this.HashBlowup(t12);
		hpad = '011111110';
		hpad = this.toHex(hpad);
		var inpb = pi.toString() + hpad + h;
		
		inpb = inpb.replace('0x','');
		inpb = new sjcl.bn(inpb);
		
		var W = PRE.xorAll(inpa, inpb);
		
		var t1 = this.SHA256_str2bn(pki_2.toString().replace('0x','')); 
		var t2 = ski_1.mulmod(t1, this.q);
		var t3 = t2.add(ski_2);
		var t4 = t3.reduce(this.q);
		var t5 = t4.inverseMod(this.q);
		
		var RKi_j = htemp.mulmod(t5, this.q);
		rekey_Keyobj.RKi_j = RKi_j;
		rekey_Keyobj.V = V;
		rekey_Keyobj.W = W;
		
		return rekey_Keyobj;
	},
	/*-----------------------------------------------------------------------------
	Convert ReKEy key object to string.
	-----------------------------------------------------------------------------*/	
	Obj2StrReKey : function(rekey_Keyobj)
	{
		RKi_j = rekey_Keyobj.RKi_j.toString().replace('0x','');
		V = rekey_Keyobj.V.toString().replace('0x','');
		W = rekey_Keyobj.W.toString().replace('0x','');

		return [RKi_j, V, W];
	},	
	/*-----------------------------------------------------------------------------
	Convert Public key string to object.
	-----------------------------------------------------------------------------*/	
	Str2ObjReKey : function(RKi_j, V, W)
	{
		var rekey_Keyobj = rekey_Struct;
		rekey_Keyobj.RKi_j = new sjcl.bn(RKi_j);
		rekey_Keyobj.V = new sjcl.bn(V);
		rekey_Keyobj.W = new sjcl.bn(W);

		return rekey_Keyobj;
	},
	/*-----------------------------------------------------------------------------
	Encryption function. 
	Input is public_Keyobj_i of user i and the message m. 
	Output is a first level ciphertext for user i.
	-----------------------------------------------------------------------------*/		
	Encrypt1 : function(public_Keyobj_i, m)
	{
		var ct1_CTobj = ct1_Struct;
		var pki_1 = public_Keyobj_i.pk_1;
		var pki_2 = public_Keyobj_i.pk_2;
		var g = new sjcl.bn(this.g);

		var u = new sjcl.bn(u);	
		var u = sjcl.bn.random(this.q);		
		var w = new sjcl.bn(w);
		var w = sjcl.bn.random(this.q);

		var t1 = this.SHA256_str2bn(pki_2.toString().replace('0x','')); 
		var t2 = pki_1.powermod(t1, this.p);
		var t3 = t2.mulmod(pki_2, this.p);

		var D = t3.powermod(u, this.p);

		m = m.toString().replace('0x','');	
		w = w.toString().replace('0x','');	
		var r = this.SHA256_str2bn(m+w); 

		var E = t3.powermod(r, this.p);

		var t5 = g.powermod(r, this.p);
		inpa = this.HashBlowup(t5);

		mpad = '011111110';
		mpad = this.toHex(mpad);
		var inpb = w.toString() + mpad + m;
		inpb = inpb.replace('0x','');
		inpb = new sjcl.bn(inpb);

		var F = PRE.xorAll(inpa, inpb);

		var t62 = this.SHA256_str2bn(D.toString().replace('0x','')+E.toString().replace('0x','')+F.toString().replace('0x','')); 
		r = new sjcl.bn(r);		
		var t6 = r.mulmod(t62, this.q);
		var t7 = t6.add(u);

		var s = t7.reduce(this.q);
			
		ct1_CTobj.level = 1;
		ct1_CTobj.D = D;
		ct1_CTobj.E = E;
		ct1_CTobj.F = F;
		ct1_CTobj.s = s;

		return ct1_CTobj;
	},
	/*-----------------------------------------------------------------------------
	Convert First level Ciphertext object to string Array.
	-----------------------------------------------------------------------------*/	
	Obj2StrCT1 : function(ct1_CTobj)
	{	
		level = ct1_CTobj.level;
		D = ct1_CTobj.D.toString().replace('0x','');
		E = ct1_CTobj.E.toString().replace('0x','');
		F = ct1_CTobj.F.toString().replace('0x','');
		s = ct1_CTobj.s.toString().replace('0x','');

		return [level, D, E, F, s];
	},	
	/*-----------------------------------------------------------------------------
	Convert First level Ciphertext string to object.
	-----------------------------------------------------------------------------*/	
	Str2ObjCT1 : function(level, D, E, F, s)
	{
		var ct1_CTobj = ct1_Struct;

		ct1_CTobj.level = level;
		ct1_CTobj.D = new sjcl.bn(D);
		ct1_CTobj.E = new sjcl.bn(E);
		ct1_CTobj.F = new sjcl.bn(F);
		ct1_CTobj.s = new sjcl.bn(s);		
		
		return ct1_CTobj;
	},	
	/*-----------------------------------------------------------------------------
	Re-Encryption function. 
	Input is rekey from i to j, first level ciphertext, 
		key objects of delegatee and delegator. 
	Output is a second level ciphertext for user j.
	-----------------------------------------------------------------------------*/		
	ReEncrypt : function(rekey_Keyobj, ct1_CTobj, Keyobj_i, Keyobj_j)
	{
		var ct2_CTobj = ct2_Struct;
		pki_1 = Keyobj_i.pk_1;
		pki_2 = Keyobj_i.pk_2;
		
		pkj_1 = Keyobj_j.pk_1;
		pkj_2 = Keyobj_j.pk_2;
		
		RKi_j = rekey_Keyobj.RKi_j;
		V = rekey_Keyobj.V;
		W = rekey_Keyobj.W;
		
		D = ct1_CTobj.D;
		E = ct1_CTobj.E;
		F = ct1_CTobj.F;
		s = ct1_CTobj.s;
			
		var t1 = this.SHA256_str2bn(pki_2.toString().replace('0x','')); 
		var t2 = pki_1.powermod(t1, this.p);
		var t3 = t2.mulmod(pki_2, this.p);
		var t4 = t3.powermod(s, this.p);
		var tempt4 = t3;
		var LHS = t4;
		var hashinput = D.toString().replace('0x','') + E.toString().replace('0x','') + F.toString().replace('0x','');
		var t51 = this.SHA256_str2bn(hashinput); 
		var t6 = E.powermod(t51, this.p);
		var RHS = D.mulmod(t6, this.p);
		
		if (LHS.equals(RHS) == true)
		{
			ct2_CTobj.level = 2;
			ct2_CTobj.E1 = E.powermod(RKi_j, this.p);
			ct2_CTobj.F = F;
			ct2_CTobj.V = V;
			ct2_CTobj.W = W;
			
			return ct2_CTobj;
		}
		else
		{
			return "Invalid first level Ciphertext ";
		}
	},
	/*-----------------------------------------------------------------------------
	Convert Second level Ciphertext object to string Array.
	-----------------------------------------------------------------------------*/	
	Obj2StrCT2 : function(ct2_CTobj)
	{	
		level = ct2_CTobj.level;
		E1 = ct2_CTobj.E1.toString().replace('0x','');
		F = ct2_CTobj.F.toString().replace('0x','');
		V = ct2_CTobj.V.toString().replace('0x','');
		W = ct2_CTobj.W.toString().replace('0x','');

		return [level, E1, F, V, W];
	},	
	/*-----------------------------------------------------------------------------
	Convert Second level Ciphertext string to object.
	-----------------------------------------------------------------------------*/	
	Str2ObjCT2 : function(level, E1, F, V, W)
	{
		var ct2_CTobj = ct2_Struct;

		ct2_CTobj.level = level;
		ct2_CTobj.E1 = new sjcl.bn(E1);
		ct2_CTobj.F = new sjcl.bn(F);
		ct2_CTobj.V = new sjcl.bn(V);
		ct2_CTobj.W = new sjcl.bn(W);		
		
		return ct2_CTobj;
	},
	/*-----------------------------------------------------------------------------
	Decryption function. 
	Input is secret key and public key of the receiver and the ciphertext, 
	Output is the message.
	-----------------------------------------------------------------------------*/		
	Decrypt : function(secret_Keyobj_i, public_Keyobj_i, ct_CTobj)
	{
		pki_1 = public_Keyobj_i.pk_1;
		pki_2 = public_Keyobj_i.pk_2;
		ski_1 = secret_Keyobj_i.sk_1;
		ski_2 = secret_Keyobj_i.sk_2;
		
		if(ct_CTobj.level == 1)
		{		
			var t1 = this.SHA256_str2bn(pki_2.toString().replace('0x','')); 
			var t2 = pki_1.powermod(t1, this.p);
			var t3 = t2.mulmod(pki_2, this.p);
			var t4 = t3.powermod(ct_CTobj.s, this.p);
			var tempt4 = t3;
			var LHS = t4;
			
			var hashIP = ct_CTobj.D.toString().replace('0x','') + ct_CTobj.E.toString().replace('0x','') + ct_CTobj.F.toString().replace('0x','');
			var t51 =this.SHA256_str2bn(hashIP); 
			var t6 = ct_CTobj.E.powermod(t51, this.p);
			var RHS = ct_CTobj.D.mulmod(t6, this.p);
			
			if (LHS.equals(RHS) == true)
			{
				var t1 = this.SHA256_str2bn(pki_2.toString().replace('0x','')); 
				var t2 = ski_1.mulmod(t1, this.q);
				var t3 = t2.add(ski_2);
				var t4 = t3.reduce(this.q);
				var t5 = t4.inverseMod(this.q);
				var t6 = ct_CTobj.E.powermod(t5, this.p);
				inpa = this.HashBlowup(t6);

				var mw = PRE.xorAll(inpa, ct_CTobj.F);
				mw = mw.toString();
				mw = mw.replace('0x','');
				mw = mw.split("303131313131313130");
				w1 = mw[0];
				w1 = new sjcl.bn(w1);
				m1 = mw[1];
				var r1 = this.SHA256_str2bn(m1 + w1.toString().replace('0x','')); 
				if (tempt4.powermod(r1, this.p).equals(ct_CTobj.E) == true)
				{
					return m1;				
				}
				else
				{
					console.log("Invalid Ciphertext Test 2");
					return "Invalid Ciphertext: Failed Test 2";
				}
			}
			else
			{
				console.log("Invalid Ciphertext Test 1");
				return "Invalid Ciphertext: Failed Test 1";
			}
		}
		else if (ct_CTobj.level == 2)
		{
			E1 = ct_CTobj.E1;
			F = ct_CTobj.F;
			V = ct_CTobj.V;
			W = ct_CTobj.W;
			
			var t1 = ski_2.inverseMod(this.q);
			var t2 = V.powermod(t1, this.p);
			inpa = this.HashBlowup(t2);

			var hpi = PRE.xorAll(inpa, W);
			hpi = hpi.toString();
			hpi = hpi.replace('0x','');
			hpi = hpi.split("303131313131313130");
		    pi = hpi[0];
			h = hpi[1];
			h = new sjcl.bn(h);
			var h2 = E1.powermod(h.inverseMod(this.q), this.p);
			inpa = this.HashBlowup(h2);
			
			var mw = PRE.xorAll(inpa, F);
			var mw = PRE.xorAll(inpa, ct_CTobj.F);
			mw = mw.toString();
			var mw = mw.replace('0x','');
			mw = mw.split("303131313131313130");
			w1 = mw[0];
			w1 = new sjcl.bn(w1);
			m1 = mw[1];
			var r1 = this.SHA256_str2bn(m1 + w1.toString().replace('0x','')); 
			h = h.toString().replace('0x','');	
			pi = pi.toString().replace('0x','');
			var v = this.SHA256_str2bn(h + pi); 
			V1 = pki_2.powermod(v, this.p);
			if (V.equals(V1) == true)
			{
				var E11 = this.g.powermod(r1, this.p);
				var E12 = E11.powermod(h, this.p);
				if (E12.equals(E1) == true)
				{
					return m1;				
				}
				else
				{
					console.log("Invalid Ciphertext - Test 2");
					return "Invalid Ciphertext - Failed Test 2";
				}
			}
			else
			{
				console.log("Invalid Ciphertext - Test 1");
				return "Invalid Ciphertext - Failed Test 1";
			}
		}
	},	
	/*-----------------------------------------------------------------------------
	Function to test PRE	
	-----------------------------------------------------------------------------*/	
	testPRE : function()
	{
	PRE.Gen_Safeprimes();
	
	//MSK = PRE.Gen_MSK();
	//EMSK = PRE.Encrypt_MSK("password", MSK);
	//MSK = PRE.Decrypt_MSK("password", EMSK);
	//console.log(PRE.Derive_FEK(MSK)[1].toString());
	//PRE.KeyGen(MSK);

	var start = new Date();
	for(j =1; j<2; j++)
		{
		console.log("iteration :" + j);
		MSK1 = PRE.Gen_MSK();
		MSK2 = PRE.Gen_MSK();
		let keyobj1 = Object.assign({},PRE.KeyGen(MSK1));
		let keyobj2 = Object.assign({},PRE.KeyGen(MSK2));	
//		m = sjcl.bn.random(PRE.q);
//		m = new sjcl.bn(m);
		m = PRE.Derive_FEK(MSK1)[1];
		console.log("key is : " + m);
	// Test Public key obj2str2obj conversion
		let pubKeyobj1 =  Object.assign({},PRE.ExtractPubKey(keyobj1));
		pki = PRE.Obj2StrPubKey(pubKeyobj1);
		pubKeyobj1 = Object.assign({},PRE.Str2ObjPubKey(pki[0],pki[1]));
		let pubKeyobj2 =  Object.assign({},PRE.ExtractPubKey(keyobj2));	
	// Test ciphertext I obj2str2obj conversion
		ct1_CTobj = PRE.Encrypt1(pubKeyobj1, m);
		ct = PRE.Obj2StrCT1(ct1_CTobj);
		ct1_CTobj = PRE.Str2ObjCT1(ct[0],ct[1],ct[2],ct[3],ct[4]);
		console.log("Cipher is "+ct1_CTobj)
	// Test ReKey obj2str2obj conversion		
		rekey_Keyobj = PRE.ReKeyGen(PRE.ExtractSecKey(keyobj1), pubKeyobj1, pubKeyobj2);
		rk = PRE.Obj2StrReKey(rekey_Keyobj);
		rekey_Keyobj = PRE.Str2ObjReKey(rk[0],rk[1],rk[2]);
	// Test ciphertext II obj2str2obj conversion
		ct2_CTobj = PRE.ReEncrypt(rekey_Keyobj, ct1_CTobj, keyobj1, keyobj2);
		CT2 = PRE.Obj2StrCT2(ct2_CTobj);
		ct2_CTobj = PRE.Str2ObjCT2(CT2[0], CT2[1], CT2[2], CT2[3], CT2[4]);		
		
		var m1 = PRE.Decrypt(PRE.ExtractSecKey(keyobj1), pubKeyobj1, ct1_CTobj);
		var m2 = PRE.Decrypt(PRE.ExtractSecKey(keyobj2), pubKeyobj2, ct2_CTobj);
		console.log("Decrypted Message is :" + m1);
		console.log("Decrypted Message is :" + m2);
		}
	var time = new Date() - start;
	console.log("Time taken is :" + (time/1000) + " Seconds");
	},
};

//PRE.testPRE();


function addStorage(){
        var GenRandom =  {
	
    Stored: [],
	
	Job: function(){
		var newId = Date.now().toString().substr(6); // or use any method that you want to achieve this string
		
        if( !this.Check(newId) ){
            this.Stored.push(newId);
            return newId;
        }
        
        return this.Job();
	},
	
	Check: function(id){
		for( var i = 0; i < this.Stored.length; i++ ){
			if( this.Stored[i] == id ) return true;
		}
		return false;
	}
	
};
	var name=$('#storagename').val();
        
        var type = $('input[name=storagename]:checked').val();
	var isPreferred=$('#isPreferred').prop('checked');
        console.log("type is"+type);
        if(name == ''){
          var randam = (GenRandom.Job());
          name = type+'_'+randam;
        }
	$.ajax({
 	type: "POST",
  	url: 'user/storage',
 	data: {'name':name,'type':type,'isPreferred':isPreferred},
  	success: function (msg,status,jqXHR){
		console.log("Storage Added  "+name);
	        window.location = msg;
	},
  	dataType: 'text'
	});

	return;
}

$(function() {

  $("#passphraseform").validate({
    rules: {
      passphrase: {
        required: true,
        minlength: 6,
      },
    },
    messages: {
      passphrase: {
        required: "Please enter password",
        minlength: "Your password must be at least 6 characters",
      },
    }
  });
});

$.validator.addMethod("validatePassword", function(value, element) {
   return validatePassword(value);
}, "Password doesn't match with Database , please try again");


$.validator.addMethod("checksamepassword", function(value, element) {
   return checksamepassword(value);
}, "Password doesn't match , please try again");

function setPassword(event){
	
	event.preventDefault();
	if(localStorage.getItem('validation')!=1){
		return;
	}

	var data =$('#passphrase').val();
	console.log(data);
        length=data.length;
	localStorage.setItem('passphrase',data);
	$('#passphraseModal').modal('hide');
}

function registerPassword(event){
	
	event.preventDefault();
	var data =$('#newpassphrase').val();
	var data1 =$('#confirmpassphrase').val();

	console.log("Registering new password "+data);
	if(data != data1 || (data == "" && data1 == "")){ return false;}
        else{	
	console.log("Registering new password");
	var data =$('#newpassphrase').val();
	console.log(data);
        length=data.length;
	localStorage.setItem('passphrase',data);
	setEMSK();
        }
        $('#registerPassphraseModal').modal('hide');
}

var checksamepassword = function(value){

	var data =$('#newpassphrase').val();
	var data1 =$('#confirmpassphrase').val();

	console.log("Registering new password "+data);
	if(data===data1){ return true;}
	return false;
}	

var validatePassword =function(password){

	try{
	var fileContent="ABC";	
	var MSK = PRE.Decrypt_MSK(password,JSON.stringify(getEMSKFromStorage())) 
	var FEK = PRE.Derive_FEK(MSK);
        var aes_key=FEK[1];
	var encryptedContent = sjcl.encrypt(aes_key.toString().replace('0x',''),fileContent,{cipher:"aes",count:2048,ks:128});
	var cipherText = encryptAESKey(aes_key); //will encrypt AES with PK from DB

	let keyobj = Object.assign({},PRE.KeyGen(MSK));
        let pubKeyobj =  Object.assign({},PRE.ExtractPubKey(keyobj));
        pki = PRE.Obj2StrPubKey(pubKeyobj);
        pubKeyobj = Object.assign({},PRE.Str2ObjPubKey(pki[0],pki[1]));
        var aes_key1 = PRE.Decrypt(PRE.ExtractSecKey(keyobj), pubKeyobj, cipherText);
	
	//var aes_key1 = getAESKeyFromCipher(cipherText); // will decrypt from generated secret key
	var plainText = decryptContent(encryptedContent,aes_key1);

	if(fileContent==plainText){
		localStorage.setItem("validation",1);
		return true;
	}
	return false;
	}catch(error){

		return false;
	}
}
getUserPKI = function(userId,callback){


	$.ajax({
  	type: "GET",
  	url: 'user/getUsersPK/'+userId,
  	success: function (msg,status,jqXHR){
		console.log("Public Key fetched  "+msg);
		if(callback){
		callback(wrapPKwithSJCL(JSON.parse(msg)));
		}
	},

	});
}

getEMSK = function(callback){

	$.get("user/EMSK", function(data, status){
			if(data){
			localStorage.setItem('EMSK',data);
			getPKI(callback);
			}else{
			callback();
			}
			//if there is no keys , generate EMSK and set it in DB
			}).fail(function(){
				console.log("Getting EMSK Failed");
				//setEMSK();
				});
}


getPKI = function(callback){

	$.get("user/publicKey", function(data, status){
			if(!data){
			setPKI();//createNew EMSK
			}else{
			localStorage.setItem('publicKey',data);
			}
				if(callback){
				callback(data);
				}
			//if there is no keys , generate EMSK and set it in DB
			}).fail(function(){
				console.log("Getting Public Key Failed");
				//setEMSK();
				});
}

function setPKI(){

	var pki=generatePublicKey();
	pki.pk_1=pki.pk_1.toString();
	pki.pk_2=pki.pk_2.toString();
	console.log(pki);
	$.ajax({
  	type: "PUT",
  	url: 'user/publicKey',
 	data:JSON.stringify(pki),
  	success: function (msg,status,jqXHR){
		console.log("Public Key Added  "+msg);
		localStorage.setItem('publickey',msg);
	},
	});
	

}

function getCurrentUserSecretKey (){
	
        if(!localStorage.getItem('secretKey') ){ 
		localStorage.setItem('secretKey',JSON.stringify(generateSecretKey()));
	}
        var skObj = JSON.parse(localStorage.getItem('secretKey'));
	return wrapSKwithSJCL(skObj);
}

function getCurrentUserPublicKey (){
	
        if(!localStorage.getItem('publicKey') ){ 
		//getPKI();
		localStorage.setItem('publicKey',generatePublicKey());
	}
        var pkObj = JSON.parse(localStorage.getItem('publicKey'));
		console.log("pkObj"+pkObj);
	return wrapPKwithSJCL(pkObj);
}


function generateSecretKey(){

	var MSK = getMSKFromStorage();
	let keyobj = Object.assign({},PRE.KeyGen(MSK));

	var sec_key = PRE.ExtractSecKey(keyobj);

	return sec_key;
}

function generatePublicKey(){


	var MSK = getMSKFromStorage();
	let keyobj = Object.assign({},PRE.KeyGen(MSK));
	let pubKeyobj =  Object.assign({},PRE.ExtractPubKey(keyobj));
	pki = PRE.Obj2StrPubKey(pubKeyobj);
	pubKeyobj = Object.assign({},PRE.Str2ObjPubKey(pki[0],pki[1]));

	return pubKeyobj;

}



function setEMSK(){

	var password = localStorage.getItem("passphrase");
	console.log(password);
	var MSK = PRE.Gen_MSK();
	var EMSK = PRE.Encrypt_MSK(password, MSK);
	$.ajax({
  	type: "PUT",
  	url: 'user/EMSK',
 	data: EMSK,
  	success: function (msg){
		console.log("EMSK set");
		localStorage.setItem('EMSK',JSON.stringify(msg));
		setPKI();
	},
	});
}

function getEMSKFromStorage(){
	return JSON.parse(localStorage.getItem('EMSK'));
}
function getPasswordFromStorage(){
	return localStorage.getItem('passphrase');
}
function getMSKFromStorage(){
	
	return PRE.Decrypt_MSK(getPasswordFromStorage(),JSON.stringify(getEMSKFromStorage()));
}

function encryptAESKey(aes_key){

	var pubKeyobj = getCurrentUserPublicKey();
	var FEK_Cipher = PRE.Encrypt1(pubKeyobj, aes_key);//encrypted AES KEY
	var ct = PRE.Obj2StrCT1(FEK_Cipher);//cipherText
	var cipherTextObj = PRE.Str2ObjCT1(ct[0],ct[1],ct[2],ct[3],ct[4]);
	
	return cipherTextObj;
}



function deriveKeys(){


	var MSK = getMSKFromStorage();
	var pubKeyobj = getCurrentUserPublicKey();
	var FEK = PRE.Derive_FEK(MSK);
	var aes_key=FEK[1];
	var FEK_Cipher = PRE.Encrypt1(pubKeyobj, aes_key);//encrypted AES KEY
	console.log(FEK_Cipher);
	var ct = PRE.Obj2StrCT1(FEK_Cipher);//cipherText
	var cipherTextObj = PRE.Str2ObjCT1(ct[0],ct[1],ct[2],ct[3],ct[4]);
	console.log(cipherTextObj);

	return [cipherTextObj,aes_key,FEK[0]];
}

function encryptContent(fileContent,keys){

	if(!fileContent){
		return "";
	}

	console.log(keys[1]);
	var result = sjcl.encrypt(keys[1].toString().replace('0x',''),fileContent,{cipher:"aes",count:2048,ks:128});
	console.log(result);
	console.log("RESULT");
	cipherString=getCipherString(keys[0]);
	//prefix nonce,cipherObj to the content
	return {'cipher':cipherString,'content':result};
}

function getCipherString(cipherTextObj){

	cipherTextObj.D=cipherTextObj.D.toString();
	cipherTextObj.E=cipherTextObj.E.toString();
	cipherTextObj.F=cipherTextObj.F.toString();
	cipherTextObj.s=cipherTextObj.s.toString();

	return cipherTextObj;
}

function getAESKeyFromCipher(cipherText){

	var MSK = getMSKFromStorage();
	console.log("msk"+MSK);
	let keyobj = Object.assign({},PRE.KeyGen(MSK));
	let pubKeyobj =  Object.assign({},PRE.ExtractPubKey(keyobj));
	pki = PRE.Obj2StrPubKey(pubKeyobj);
	pubKeyobj = Object.assign({},PRE.Str2ObjPubKey(pki[0],pki[1]));
	var AES_PLAIN = PRE.Decrypt(PRE.ExtractSecKey(keyobj), pubKeyobj, cipherText);
	console.log("aes plain"+AES_PLAIN);

	return AES_PLAIN;
}

function decryptContent(encryptedContent,aes_key){

	if(!encryptedContent){
		return "";
	}

	var result = sjcl.decrypt(aes_key.toString().replace('0x',''),encryptedContent,{cipher:"aes",count:2048,ks:128});
	

	return result;
}

function wrapPKwithSJCL(pkObj){

	var pubKey = pkObj;
	
	pubKey.pk_1 = new sjcl.bn(pkObj.pk_1);
	pubKey.pk_2 = new sjcl.bn(pkObj.pk_2);

	return pubKey;
}

function wrapSKwithSJCL(skObj){

	var secKey = skObj;
	
	secKey.sk_1 = new sjcl.bn(skObj.sk_1);
	secKey.sk_2 = new sjcl.bn(skObj.sk_2);

	return secKey;
}

function object2SJCL(cipher){


	var cipherTextObj = cipher;


	if(cipherTextObj.level==2){
		//second level cipher
		cipherTextObj.E1 = new sjcl.bn(cipher.E1);
		cipherTextObj.F = new sjcl.bn(cipher.F);
		cipherTextObj.V = new sjcl.bn(cipher.V);
		cipherTextObj.W = new sjcl.bn(cipher.W);
	
	}else{

		cipherTextObj.D = new sjcl.bn(cipher.D);
		cipherTextObj.E = new sjcl.bn(cipher.E);
		cipherTextObj.F = new sjcl.bn(cipher.F);
		cipherTextObj.s = new sjcl.bn(cipher.s);
	}
	return cipherTextObj;
}

function downloadFile(url,fileName,mimeType){

	$.get(url, function(data) {
   	        console.log("url"+url);	
		var plainText=data;	
		try{	
		
		var content = JSON.parse(data);
		var cipher = content.cipher;
		var cipherTextObj = object2SJCL(cipher);
		var fileContent = content.content;
		var aes_key = getAESKeyFromCipher(cipherTextObj);
		var plainText = decryptContent(fileContent,aes_key);
		
		downloadPlainText(plainText,fileName,mimeType);
		
		}catch(err){
	        console.log("File is not encrypted by coreshare "+err);
		downloadText(plainText,fileName,mimeType);
		}
	});



}

function downloadText(fileContent,fileName,mimeType){

var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";      
        var blob = new Blob([fileContent], {
                type: mimeType
        });
        console.log(blob);
        url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
       
}

function downloadPlainText(fileContent,fileName,mimeType){

	/*var a = document.createElement("a");
	document.body.appendChild(a);
	a.style = "display: none";	
	var blob = new Blob([fileContent], {
    		type: mimeType
	});
	console.log(blob);
	url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
	*/
	//var base64decrypt = sjcl.decrypt("aaaaa", crypt);
	/*var decrypt = sjcl.codec.base64.toBits(fileContent); // added
	console.log(decrypt);
	var byteNumbers = fromBitArrayCodec(decrypt);
	var byteArray = new Uint8Array(byteNumbers);
	console.log(byteArray);
	saveByteArray(byteArray,fileName);
	*/
	//var content = fileContent.hexDecode().toString('utf-8');	
	/*var element = document.createElement('a');
	element.setAttribute('href', fileContent.toString('utf-8'));
	element.setAttribute('download', fileName);

	element.style.display = 'none';
	document.body.appendChild(element);

	element.click();

	document.body.removeChild(element);
	*/

	var blob = dataURIToBlob(fileContent);
	var url = URL.createObjectURL(blob);
	var blobAnchor =  document.createElement('a');
	var dataURIAnchor = document.createElement('a');
	blobAnchor.download = dataURIAnchor.download = fileName;
	blobAnchor.href = url;
	dataURIAnchor.href = fileContent.toString('utf-8');
	//stat_.textContent = '';

	blobAnchor.onclick = function() {
		requestAnimationFrame(function() {
				URL.revokeObjectURL(url);
				})
	};
        document.body.appendChild(blobAnchor);
	blobAnchor.click();
        setTimeout(function(){
        document.body.removeChild(blobAnchor);
        window.URL.revokeObjectURL(url);  
    }, 100);  
//	return blobAnchor;
}

function dataURIToBlob(dataURI) {

  var binStr = atob(dataURI.split(',')[1]),
    len = binStr.length,
    arr = new Uint8Array(len),
    mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

  for (var i = 0; i < len; i++) {
    arr[i] = binStr.charCodeAt(i);
  }

  return new Blob([arr], {
    type: mimeString
  });

}


var saveByteArray = (function () {
    var a = document.createElement("a");
    document.body.appendChild(a);
    a.style = "display: none";
    return function (data, name) {
        var blob = new Blob(data, {type: "image/png"}),
            url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = name;
        a.click();
        window.URL.revokeObjectURL(url);
    };
}());

/** Convert from an array of bytes to a bitArray. */
function toBitArrayCodec(bytes) {
    var out = [], i, tmp=0;
    for (i=0; i<bytes.length; i++) {
        tmp = tmp << 8 | bytes[i];
        if ((i&3) === 3) {
            out.push(tmp);
            tmp = 0;
        }
    }
    if (i&3) {
        out.push(sjcl.bitArray.partial(8*(i&3), tmp));
    }
    return out;
}

/** Convert from a bitArray to an array of bytes. */
function fromBitArrayCodec(arr) {
    var out = [], bl = sjcl.bitArray.bitLength(arr), i, tmp;
    for (i=0; i<bl/8; i++) {
        if ((i&3) === 0) {
            tmp = arr[i/4];
        }
        out.push(tmp >>> 24);
        tmp <<= 8;
    }
    return out;
}

function parseHexString(str) { 
    var result = [];
    while (str.length >= 8) { 
        result.push(parseInt(str.substring(0, 8), 16));

        str = str.substring(8, str.length);
    }

    return result;
}

function createHexString(arr) {
    var result = "";
    var z;

    for (var i = 0; i < arr.length; i++) {
        var str = arr[i].toString(16);

        z = 8 - str.length + 1;
        str = Array(z).join("0") + str;

        result += str;
    }

    return result;
}



String.prototype.hexEncode = function(){
    var hex, i;

    var result = "";
    for (i=0; i<this.length; i++) {
        hex = this.charCodeAt(i).toString(16);
        result += ("000"+hex).slice(-4);
    }

    return result
}

String.prototype.hexDecode = function(){
    var j;
    var hexes = this.match(/.{1,4}/g) || [];
    var back = "";
    for(j = 0; j<hexes.length; j++) {
        back += String.fromCharCode(parseInt(hexes[j], 16));
    }

    return back;
}



var uploadForm = document.forms.namedItem("fileinfo");

function encryptAndUpload(fileContent,fileName,mimeType,options){
	
        document.querySelector('#uploadspinner').style.display='';
	//downloadPlainText(fileContent,fileName,mimeType);
	//console.log(fileContent);return;
	var keys = deriveKeys(); 	//will return an array of cipherText and AES key
	var encryptedContent=encryptContent(fileContent,keys); //encrypt content with the supplied AES key
      	oData = new FormData(uploadForm);
	//oData.append( "cipherText", keys[0] ); // <- adding cipherText
	//var formData = new FormData();
	var file = new File([new Blob([JSON.stringify(encryptedContent)])],fileName,{type:mimeType});
	console.log(encryptedContent);
	oData.set('file', file,fileName);
	//formData.append('another-form-field', 'some value');
	//oData.append( "_token", oData.get("_token") ); // <- adding csrf token
  	var oOutput = document.querySelector("#uploadModal");
	var oReq = new XMLHttpRequest();
	oReq.open("POST", "uploadfile", true);
	oReq.onload = function(oEvent) {
		if (oReq.status == 200) {
                        document.querySelector('#uploadspinner').style.display='none';
                        oOutput.style.display='none';
                        document.querySelector('.uploadsuccessMessage').style.display='block';
		//	oOutput.innerHTML = "Uploaded!";
		} else {
                        document.querySelector('#uploadspinner').style.display='none';
			oOutput.innerHTML = "Error " + oReq.status + " occurred when trying to upload your file.<br \/>";
		}
	};

  	oReq.send(oData);
	/*
	$.ajax({
		url: 'uploadfile',
		data: oData,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function () {
		console.log('ok');
		},
		error: function (err) {
		console.log(err); // replace with proper error handling
		}
	});
	*/
}




 function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    var output = [];
    for (var i = 0, f; f = files[i]; i++) {
      output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ') - ',
                  f.size, ' bytes, last modified: ',
                  f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a',
                  '</li>');
    } 
    console.log(output);
    //document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
  }

testEncryption = function(){

	localStorage.setItem('passphrase','password');
	var plainText = "HELLO";
	var MSK = getMSKFromStorage();
	
	let keyobj = Object.assign({},PRE.KeyGen(MSK));
	let pubKeyobj =  Object.assign({},PRE.ExtractPubKey(keyobj));
	pki = PRE.Obj2StrPubKey(pubKeyobj);
	pubKeyobj = Object.assign({},PRE.Str2ObjPubKey(pki[0],pki[1]));

	var FEK = PRE.Derive_FEK(MSK);
	var aes_key=FEK[1];
	var FEK_Cipher = PRE.Encrypt1(pubKeyobj, aes_key);//encrypted AES KEY
	console.log(FEK_Cipher);
	var ct = PRE.Obj2StrCT1(FEK_Cipher);//cipherText
	var cipherTextObj = PRE.Str2ObjCT1(ct[0],ct[1],ct[2],ct[3],ct[4]);
	
	var encryptedContent = sjcl.encrypt(aes_key.toString().replace('0x',''),plainText,{cipher:"aes",count:2048,ks:128});
		
	var aes_key1 = PRE.Decrypt(PRE.ExtractSecKey(keyobj), PRE.ExtractPubKey(keyobj), cipherTextObj);
	console.log(aes_key.toString()===aes_key1.toString());
	
	var result = sjcl.decrypt(aes_key1.toString().replace('0x',''),encryptedContent,{cipher:"aes",count:2048,ks:128});
	console.log(plainText===result);	

	localStorage.clear();
}

modifyPreferredStorage = function(){

     var storageId = $(this).data('id');
     $.ajax({
    url: 'user/preferred/'+storageId,
    type: 'PUT',
    success: function(result) {
        	// Do something with the result
     	$.get("user/storage", function(data, status){
		populateSettingsStorage(data);
   	 });
		console.log('Favourite storage updated');
    		}
	});


}
removeStorage = function(){


     var fileId = $(this).data('id');
     $.ajax({
    url: 'user/storage/'+fileId,
    type: 'DELETE',
    success: function(result) {
        // Do something with the result
     	$.get("user/storage", function(data, status){
		populateSettingsStorage(data);
   	 });
	if(result=='FAILED'){
	     $('#showmessage').modal('show');
	}
	console.log('Storage deleted');
    }
	});

}

populateTab = function(id,type){
	console.log('Going to fetch files for '+id);	
	if ($('#'+id).text().length < 10) {
		var url="/user/storage/"+id+"/files";	
     		$.get(url, function(data, status){
                                        console.log("url is"+url);
					populateContent(data,id,type);	
		});	
	} 


}

/*
setTimeout(function() {
        $("li.storagenavtabli").trigger('click');
        $(".activetab").trigger('click');
},1000);*/

populateNavBar = function(data){
//$('li.storagenavtabli').click(function() {
	$('li.storagenavtabli').each(function() {
        var clickid = $(this).attr('id');
        console.log("id id"+clickid);
        var navbar_content = $("#navtab-content-"+clickid);
	var navbar = $("#navtab-"+clickid);
        navbar_content.empty();
        navbar.empty();
	if(data!=undefined){
	var storages=JSON.parse(data);
        console.log("storages"+storages);
        console.log("storages"+clickid);
        var i=0;
        var div=document.createElement('div');
        var div=$('<div/>').attr({
                 id: ''
                }).addClass('tab-content');
	var ul=document.createElement('ul');
	var ul=$(ul).attr({
		 id: 'ul'
		}).addClass('nav nav-tabs tabs-left');
	storages.forEach(function(element) {
                        if(clickid == element.type){
			var a =$("<a/>").attr({'class':'activetab','data-type':element.type,'data-toggle':'tab','data-id':element.id,'href':'#'+element.id}).html(element.display_name);
			var li=$("<li/>").append(a);
                        var childdiv =$("<div/>").attr({ 'class':'tab-pane table-responsive','id':element.id});
                        if(i==0){
                         li=$("<li/>").attr({'class':'active activetab'}).append(a);
                         childdiv =$("<div/>").attr({ 'class':'tab-pane active  table-responsive','id':element.id});

                        }
                       //  childdiv.html(element.display_name);
			a.bind('click',function(){ 
					if($(this).data('toggle')=='tab'){
						console.log("Nav tab clicked");
                                                $('#'+element.id).empty();
						populateTab($(this).data('id'),$(this).data('type'));
					}
			});
		//	a1.bind('click',function(){ 
$(document).ready(function(){
$("#navtab-onedrive li button").click(function(){
   	 var $radios = $('input:radio[name=storagename]');
        $radios.filter('[value=onedrive]').prop('checked', true);
        jQuery("input:radio[value=google]").attr('disabled',true);
        jQuery("input:radio[value=dropbox]").attr('disabled',true);
        jQuery("input:radio[value=box]").attr('disabled',true);
        jQuery("input:radio[value=onedrive]").removeAttr( "disabled" );
        jQuery(".addstorageimageonedrive").addClass('checked');
        jQuery(".addstorageimagegoogle").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagedropbox").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagebox").removeClass('checked').addClass('disabled');
});
$("#navtab-dropbox li button").click(function(){
   	 var $radios = $('input:radio[name=storagename]');
        $radios.filter('[value=dropbox]').prop('checked', true);
        jQuery("input:radio[value=google]").attr('disabled',true);
        jQuery("input:radio[value=dropbox]").removeAttr( "disabled" );
        jQuery("input:radio[value=box]").attr('disabled',true);
        jQuery("input:radio[value=onedrive]").attr( 'disabled',true );
        jQuery(".addstorageimageonedrive").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagegoogle").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagedropbox").addClass('checked');
        jQuery(".addstorageimagebox").removeClass('checked').addClass('disabled');
});
$("#navtab-box li button").click(function(){
   	 var $radios = $('input:radio[name=storagename]');
        $radios.filter('[value=box]').prop('checked', true);
        jQuery("input:radio[value=google]").attr('disabled',true);
        jQuery("input:radio[value=dropbox]").attr('disabled',true);
        jQuery("input:radio[value=onedrive]").attr('disabled',true);
        jQuery("input:radio[value=box]").removeAttr( "disabled" );
        jQuery(".addstorageimageonedrive").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagegoogle").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagedropbox").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagebox").addClass('checked');
});
$("#navtab-google li button").click(function(){
   	 var $radios = $('input:radio[name=storagename]');
        $radios.filter('[value=google]').prop('checked', true);
        jQuery("input:radio[value=onedrive]").attr('disabled',true);
        jQuery("input:radio[value=dropbox]").attr('disabled',true);
        jQuery("input:radio[value=box]").attr('disabled',true);
        jQuery("input:radio[value=google]").removeAttr( "disabled" );
        jQuery(".addstorageimageonedrive").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagegoogle").addClass('checked');
        jQuery(".addstorageimagedropbox").removeClass('checked').addClass('disabled');
        jQuery(".addstorageimagebox").removeClass('checked').addClass('disabled');
});
});
			ul.append(li);
                        div.append(childdiv);
                        i++; 
			//li.innerHTML=li.innerHTML + element.display_name;
         }
	});
			var a1 =$("<button/>").attr({'class':'btn btn-primary','data-toggle':'modal','data-target':'#addstorage','data-backdrop':'static','data-keyboard':'false'}).html('ADD STORAGE');
			var li1=$("<li/>").append(a1);
               
        ul.append(li1);
	}
        navbar_content.append(div);
	navbar.append(ul);
	//if(storages.length>0){
	//populateTab(storages['0'].id,storages['0'].type);
	//}

	});
                     if(window.location.pathname == '/user'){
                         setTimeout(function(){ $($('#navtab-google').find('li')[0]).find('a').click();},1000);
                      }

}

populateContent = function(data,id,type){

        var content_div = $("#"+id);
	var files=JSON.parse(data);


	var table = $('<div></div>').addClass('rcmcontainer col-xs-12');
	//var thead = $($.parseHTML('<thead><tr><th><input type="checkbox" value=""></th><th>Name</th><th>Share</th><th>Delete</th></tr></thead>'));
//	table.append(thead);
        var GenRandom =  {

    Stored: [],

        Job: function(){
                var newId = Date.now().toString().substr(6); // or use any method that you want to achieve this string

        if( !this.Check(newId) ){
            this.Stored.push(newId);
            return newId;
        }

        return this.Job();
        },

        Check: function(id){
                for( var i = 0; i < this.Stored.length; i++ ){
                        if( this.Stored[i] == id ) return true;
                }
                return false;
        }

};

	
	files.forEach(function(file) {
		if(file.mimeType=='application/vnd.google-apps.folder' || file.mimeType== 'application/folder' || file.mimeType== 'folder' ){ //folder type
		
		var row=$($.parseHTML('<div class="col-xs-3 storageblock" id="'+res+'"  data-delete="/deletefile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-id="'+file.id+'" data-storage="'+id+'" data-filename="'+file.name+'" data-mimeType="'+file.mimeType+'"  data-download="/showfile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-type="'+type+'" ><i class="fa fa-folder-o ffview"></i><div class="col-xs-12 ffdiv"><span class="glyphicon glyphicon-folder-open col-xs-2"></span><span class="col-xs-10 foldername">'+file.name+'</span></div></div>'));
		//var row=$($.parseHTML('<tbody><tr><td><input type="checkbox" value=""></td> <td><span class="glyphicon glyphicon-folder-open"></span><a href="#" class="download" data-href="/showfile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-type="'+type+'" data-mimeType="'+file.mimeType+'" data-filename="'+file.name+'" >'+file.name+'</a></td> <td><a><i class="fa fa-share-alt" data-href="/showfile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-id="'+file.id+'" data-storage="'+id+'" data-filename="'+file.name+'" data-mimeType="'+file.mimeType+'" aria-hidden="true" data-toggle="modal" data-target="#share"></i></a></td><td><a href="/deletefile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'"> Delete </a></td> </tr></tbody>'));
	 		}else{
                var randam = (GenRandom.Job());
                var res = type+randam; 
		var row=$($.parseHTML('<div class="col-xs-3 storageblock" id="'+res+'"  data-delete="/deletefile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-id="'+file.id+'" data-storage="'+id+'" data-filename="'+file.name+'" data-mimeType="'+file.mimeType+'"  data-download="/showfile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-type="'+type+'" ><i class="fa fa-file-text-o ffview"></i><div class="col-xs-12 ffdiv"><span class="glyphicon glyphicon-open-file col-xs-2"></span><span class="filename col-xs-10">'+file.name+'</span></div></div>'));

		}
		table.append(row);
	});
       // var rcmcontent =$($.parseHTML('<div class="context-menu"> <ul> <li data-id="download"><span class="Gainsboro"></span>&nbsp;<span>Download</span></li> <li data-id="share" data-toggle="modal" data-target="#share"><span class="Orange"></span>&nbsp;<span>Share</span></li> <li data-id="delete"><span class="Plum"></span>&nbsp;<span>Delete</span></li> </ul> </div> <input type="hidden" value="" id="txt_id">'));
	// var rcmh=$($.parseHTML('<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:none"> <li><a tabindex="-1" href="#" class="download" data-href="/showfile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-type="'+type+'" data-mimeType="'+file.mimeType+'" data-filename="'+file.name+'">Download</a></li> <li><a tabindex="-1"><i class="fa fa-share-alt" data-href="/showfile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'" data-id="'+file.id+'" data-storage="'+id+'" data-filename="'+file.name+'" data-mimeType="'+file.mimeType+'" aria-hidden="true" data-toggle="modal" data-target="#share"></i>Share</a></li>    <li><a tabindex="-1" href="/deletefile/'+id+'/'+file.id+'/'+file.name+'?type='+type+'">Delete</a></li>    </ul>'));
      //table.append(rcmcontent);
	content_div.append(table);




/*	$(table).on("click", ".download", function (ev) {
			var url = $(this).data('href');
		        console.log("data"+data);
			var mimeType = $(this).data('mimeType');
			var fileName = $(this).data('filename');
			console.log("mimetype is"+mimeType);
			if(!mimeType){ mimeType='application/octet-stream'}
				downloadFile(url,fileName,mimeType);
				ev.preventDefault();
			});
*/
// Trigger action when the contexmenu is about to be shown
$('.storageblock').bind("contextmenu", function(event) {

  event.preventDefault();
    var id = this.id;
  $("#txt_id").val(id);
  $(".context-menu").finish().toggle(100).

  css({
    top: event.pageY + "px",
    left: event.pageX + "px"
   });
  // disable default context menu
  return false;
 });

// If the Element is clicked somewhere
$('body').bind("mousedown", function(e) {
  if (!$(e.target).parents(".context-menu").length > 0) {
    $(".context-menu").hide(100);
  $("#txt_id").val("");
  }
});

}
 // Clicked context-menu item
 $('.context-menu li').click(function(){
  
  var titleid = $('#txt_id').val();
   console.log("titleid val is"+titleid);
  var length = titleid.length;
   console.log("titleid is"+length);
  var className = $(this).find("span:nth-child(1)").attr("class");
  $( "#"+ titleid ).css( "background-color", className );
 
   var action = $(this).data('id');

  var item=$("#"+ titleid );   
   console.log("id is"+item);
   if(action=="download"){

   var url = $(item).data('download');
   var mimeType = $(item).data('mimeType');
   var fileName = $(item).data('filename');
   console.log("mimetype is"+mimeType);
   if(!mimeType){ mimeType='application/octet-stream'}
   console.log(url+"       "+fileName+"   "+mimeType)	  
   downloadFile(url,fileName,mimeType);
   //event.preventDefault();


   }else if(action=="share"){
     document.querySelector('#sharespinner').style.display='none';
     var fileId = $(item).data('id');
     var fileName = $(item).data('filename');
     var storage = $(item).data('storage');
     var url = $(item).data('download');
     console.log(fileName);
     $(".modal-body #url").val( url );
     $(".modal-body #fileId").val( fileId );
     $(".modal-body #fileName").val( fileName );
     $(".modal-body #storage").val( storage );

   }else if(action=="delete"){
	   var deleteUrl = $(item).data('delete');
	   console.log(deleteUrl);
	   $.ajax({url: deleteUrl, success: function(result){
				console.log("File Deleted");
				//populateTab($(item).data('storage'),$(item).data('type'));
        			$("#"+$(item).data('storage')).empty();//clear file list
				
				var url="/user/storage/"+$(item).data('storage')+"/files";	
				$.get(url, function(data, status){

                                        populateContent(data,$(item).data('storage'),$(item).data('type'));
                		});

			   }});
	
   }


   $(".context-menu").hide();
 });







populateUploadOptions = function(data){

        var storage = $("#uploadstoragediv");
        var select=document.createElement('select');
        var select=$(select).attr({
            name:'storage'
          }).addClass('form-control');
        if(data!=undefined){
        var storages=JSON.parse(data);
        //console.log(storages);
         storages.forEach(function(element) {
                        var option =$("<option/>").attr({'value':element.id}).html(element.display_name);
                        //li.innerHTML=li.innerHTML + element.display_name;
                         select.append(option);
        });
        }
        storage.append(select);



}



populatePrefStorage = function(data){


        var storage = $("#prefstoragediv");
        var select=document.createElement('select');
        var select=$('<select/>').attr({
            id:'prefstorage'
          }).addClass('form-control');
        if(data!=undefined){
        var storages=JSON.parse(data);
        console.log(storages);
         storages.forEach(function(element) {
                        var option =$("<option/>").html(element.display_name);
                        //li.innerHTML=li.innerHTML + element.display_name;
                         select.append(option);
        });
        }
        storage.append(select);

}

populateShareMessages = function (data){

	
        var sharePanel = $("#sharePanel");
	sharePanel.empty();
        var table=$('<table/>').addClass('table table-bordered');
        var tbody=$('<tbody/>');
        	
        //var div=document.createElement('div');
	console.log(data);
	var shares=data;
	shares.forEach(function(element) {
			element.fileData=JSON.parse(element.fileData);
        		var div=$('<tr/>').attr({
                	 id: 'share_'+element.id
                	});
                        //console.log(element);
                        var span =$("<td/>").html(element.first_name+" want to share the file "+element.fileData.fileName+" with you");
                        div.append(span);
			var accept = $("<button/>").attr({'type':'button','class':'btn btn-xs btn-primary waves-effect waves-light','data-status':'accept','data-id':element.id});
			accept.html('ACCEPT <span class="badge">+</span>');
			accept.bind('click',function(){
				modifyShare.call(this);
			});
                        var td1 = $("<td/>");
                        td1.append(accept);
                        div.append(td1);
			var ignore = $("<button/>").attr({'type':'button','class':'btn btn-xs btn-primary waves-effect waves-light','data-status':'ignore','data-id':element.id});
			ignore.html('IGNORE <span class="badge">-</span>');
			ignore.bind('click',function(){
				modifyShare.call(this);
			});
                        var td2 = $("<td/>");
                        td2.append(ignore);
                        div.append(td2);
                        //li.innerHTML=li.innerHTML + element.display_name;
        tbody.append(div);
        table.append(tbody);
        });
        sharePanel.append(table);
        
}
populateNotificationMessages = function (data){
        
        
        var shareMessage = $("#sharemessages,#sharemessages1");
        shareMessage.empty();

        //var div=document.createElement('div');
        var shares=data;
        shares.forEach(function(element) {
                      //  element.fileData=JSON.parse(element.fileData);
                        var div=$('<div/>').attr({
                         id: 'share_'+element.id
                        }).addClass('col-xs-12 messagespan');
                        //console.log(element);
                        var span =$("<span/>").html(element.first_name+" want to share the file "+element.fileData.fileName+" with you");
                        div.append(span);
                        var accept = $("<button/>").attr({'type':'button','class':'btn btn-xs btn-primary waves-effect waves-light','data-status':'accept','data-id':element.id})
                        accept.html('ACCEPT <span class="badge">+</span>');
                        accept.bind('click',function(){
                                modifyShare.call(this);
                        });
                        div.append(accept);
                        var ignore = $("<button/>").attr({'type':'button','class':'btn btn-xs btn-primary waves-effect waves-light','data-status':'ignore','data-id':element.id})
                        ignore.html('IGNORE <span class="badge">-</span>');
                        ignore.bind('click',function(){
                                modifyShare.call(this);
                        });
                        div.append(ignore);
                        //li.innerHTML=li.innerHTML + element.display_name;
        shareMessage.append(div);
        });

}

modifyShare= function(){


     var shareId = $(this).data('id');
     var status = $(this).data('status');

    console.log(shareId+" ::: "+status);
     var fileId = $(this).data('id');
     $.ajax({
    url: 'user/shares/'+shareId+'/'+status,
    type: 'PUT',
    success: function(result) {
        	// Do something with the result
		fetchShareMessages();
		console.log('Shares status deleted');
    		}
	});

}

fetchShareMessages= function(){
	$.get("user/shares", function(data, status){
		populateShareMessages(data);
                populateNotificationMessages(data);
	});
}


function messageNotification() {
		$.ajax({
			url: "user/shares",
			type: "GET",
			processData:false,
			success: function(data){
                       
		                  fetchShareMessages();
		     	     //   $("#notification-count").remove();					
				$("#sharemessages").show();
                               // $("#notification-count").html(data);
			},
			error: function(error){
                        }           
		});
	 }
	       $(document).ready(function() { 
		$('body').click(function(event){
			if ( event.target.id != 'sharemessages'){
                               // alert(event.target.id);
				$("#sharemessages").hide();
			}
		});
		});
populateSettingsStorage = function (data){
console.log("hhihiihih");

  //$.get("user/storage", function(data, status){
        var addstorage = $("#addstorages");
	addstorage.empty(); //reset the storages	
        var div=document.createElement('div');
        var div=$('<div/>').attr({
                 id: ''
                }).addClass('col-xs-12');

	var storages=JSON.parse(data);
	storages.forEach(function(element) {
                        //console.log(element);
			var star = $("<span/>").attr({'class':'glyphicon glyphicon-star-empty','data-id':element.id,'data-dismiss':'modal'})
			if(element.preferred==1){
			star = $("<span/>").attr({'class':'glyphicon glyphicon-star','data-id':element.id,'data-dismiss':'modal'})
			 }
			var a1 =$("<a/>").attr({ 'class':'preferred-strorage pull-right','data-id':element.id,'href':'#'+element.id}).html(star);
	a1.bind('click',function(){
		modifyPreferredStorage.call(this);
		console.log("Preferred storage modificed");
	});
                        var span =$("<span/>").html(element.display_name);
                        var div2 =$("<div/>").attr({'class':'img img-responsive '+element.type});
			var icon = $("<button/>").attr({'class':'fa fa-close close','data-id':element.id,'data-dismiss':'modal'})
			var a =$("<a/>").attr({ 'class':'storage','data-id':element.id,'href':'#'+element.id}).html(icon);
                        var div1 =$("<div/>").addClass('col-xs-6 col-sm-3 col-md-3 storagediv').append(a).append(div2).append(span).append(a1);
                        div.append(div1);
                        //li.innerHTML=li.innerHTML + element.display_name;
        });
        addstorage.append(div);
	$('.storage').bind('click',function(){
		removeStorage.call(this);
	});
   // });
}

    function log( message ) {
	console.log(message);
      //$( "<div>" ).text( message ).prependTo( "#log" );
      //$( "#log" ).scrollTop( 0 );
    }

   function bindAutoComplete(){
    $( "#shareto" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "user/getUsers",
          dataType: "json",
          data: {
            q: request.term
          },
          success: function( data ) {
            //response( data );
	    response( $.map( data, function( item ) {
		return {
		label: item.email, 
		value: item.email,
		id :item.id 
		}
	    }));
          }
        });
      },
      minLength: 3,
      select: function( event, ui ) {
	  $('#shareUserId').val(ui.item.id);
        log( ui.item ?
          "Selected: " + ui.item.label :
          "Nothing selected, input was " + this.value);
          return ui.item.label;
      },
      open: function() {
        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      },
      close: function() {
        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      }
    });
   }	


$(function () {


      PRE.Gen_Safeprimes();
     // Check for the various File API support.
     if (window.File && window.FileReader && window.FileList && window.Blob) {
     // Great success! All the File APIs are supported.
     console.log("Upload API's are supported");
     //document.getElementById('file').addEventListener('change', handleFileSelect, false);
     } else {
     alert('CoreShare will not work properly in this browser. Please upgrade !');
     }
   

	if(window.location.pathname == '/login' || window.location.pathname == '/register' || window.location.pathname == '/password/reset' || document.location.pathname.indexOf("/password/reset/") == 0){
        // localStorage.clear();
	return;
	}

     	//show spinner
	$("#spinner").show();
	getEMSK( function(){
			//stop spinner
			$("#spinner").hide();
			console.log("Spinner hidden")
			if(localStorage.getItem('EMSK') ){
				if(!localStorage.getItem('passphrase') ){
				//pop up passphrase window and store data
				console.log("Password not set");
				$('#passphraseModal').modal({backdrop: 'static', keyboard: false});
				$('#passphraseModal').modal('show');
				//localStorage.setItem('passphrase','password');
				}
			}else{
				//show register password form
				$('#registerPassphraseModal').modal({backdrop: 'static', keyboard: false});
                		$('#registerPassphraseModal').modal('show');
			}

	});
        
	/*if(!localStorage.getItem('publicKey') ){
		getCurrentUserPublicKey();
	}*/


  	if(window.location.pathname=='/sharemessage' || window.location.pathname=='/existingstorage'){
		//populate share messages here
		fetchShareMessages();
	}	
	
     $.get("user/storage", function(data, status){
       console.log(data.length);

  	if(window.location.pathname=='/sharemessage' || window.location.pathname=='/existingstorage'){
		populateSettingsStorage(data);
		populatePrefStorage(data);
		return;
   	}else{
		console.log("user not in settings page");
		populateUploadOptions(data);
		populateNavBar(data);
        if(data.length >= 3){
         $('#uploadBtn').removeClass('hidden');
	}
      }
    });
  /* 
    $.get("user/storage", function(data, status){
        var i = 0;
        var navbar_content = $("#navtab-content");
        var div=document.createElement('div');
        var div=$('<div/>').attr({
                 id: ''
                }).addClass('tab-content');
        if(data!=undefined){
        var storages=JSON.parse(data);
        storages.forEach(function(element) {
                        if(i==0){
                        var childdiv =$("<div/>").attr({ 'class':'tab-pane active','id':element.id});
                        }
                        else{
                        var childdiv =$("<div/>").attr({ 'class':'tab-pane','id':element.id}).html(element.display_name);
                        }
                        if ($('#'+element.id).is(':empty')) {
                          // do something
                        }else{ 
                        div.append(childdiv);
                        }
                        i++;
                        //li.innerHTML=li.innerHTML + element.display_name;
        });
        }
        navbar_content.append(div);
    });
 $.get("user/storage", function(data, status){
        var storage = $("#uploadstoragediv");
        var select=document.createElement('select');
        var select=$('<select/>').attr({
            id:'uploadstorage'
          }).addClass('form-control');
        if(data!=undefined){
        var storages=JSON.parse(data);
        console.log(storages);
         storages.forEach(function(element) {
                        var option =$("<option/>").html(element.display_name);
                        //li.innerHTML=li.innerHTML + element.display_name;
                         select.append(option);
        });
        }
        storage.append(select);
    });
   
      $.get("user/storage", function(data, status){
        var storage = $("#prefstoragediv");
        var select=document.createElement('select');
        var select=$('<select/>').attr({
            id:'prefstorage'
          }).addClass('form-control');
        if(data!=undefined){
        var storages=JSON.parse(data);
        console.log(storages);
         storages.forEach(function(element) {
                        var option =$("<option/>").html(element.display_name);
                        //li.innerHTML=li.innerHTML + element.display_name;
                         select.append(option);
        });
        }
        storage.append(select);
    });*/

//populateStorage();
    
    $('#storageTab li:first').tab('show'); // dont FORGET this one
    
    $('#logout').bind('click',function(){
		localStorage.clear();
    });
    bindAutoComplete(); 
     
    var rules = {
         passphrase: {
             required: true
         }
    };
    var registerrules = {
         newpassphrase : {
             required: true,
         },
          confirmpassphrase : {
             required: true,
             checksamepassword : true
         }


     };
     var messages = {
         passphrase: {
             required: "Please enter password"
         }
     };
     var registermessages = {
         newpassphrase : {
             required: "Create a new password",
         },
          confirmpassphrase : {
             required: "create a confirm password",
             checksamepassword : "Enter correct password"
         }

     };
     $("#passphraseform").validate({
         rules: rules,
         messages: messages
     });
    $("#registerpassphraseform").validate({
         registerrules: registerrules,
         registermessages: registermessages
     });

   $.get("assets/js/storagetype.json", function(data, status){
        var storage = $("#addstoragediv");
        var select=document.createElement('div');
        var select=$('<div/>').attr({
            id:'storagetype'
          }).addClass('form-control');
          var i = 0;
        data.content.forEach(function(element) {
                        var img = $("<img/>").attr({'src':element.image,'class':'img img-responsive addstorageimage'+element.storage_type});
                        if (i == 0){
                        var option =$("<input/>").attr({'type' : 'radio','name':'storagename','value':element.storage_type, 'checked' : 'checked'});
                        }else{
                        var option =$("<input/>").attr({'type' : 'radio','name':'storagename','value':element.storage_type});
                        }
                         var label =$("<label/>").addClass('radio-inline').append(option);
                         label.append(img);
                        //li.innerHTML=li.innerHTML + element.display_name;
                         select.append(label);
                         i++;
        });
        storage.append(select);
    $(".addstorageimagegoogle").click(function(){
         console.log("hi");
       $(".addstorageimagegoogle").addClass("checked");
       $(".addstorageimagedropbox").removeClass("checked");
       $(".addstorageimagebox").removeClass("checked");
       $(".addstorageimageonedrive").removeClass("checked");
    });
    $(".addstorageimagedropbox").click(function(){
         console.log("hi");
       $(".addstorageimagegoogle").removeClass("checked");
       $(".addstorageimagedropbox").addClass("checked");
       $(".addstorageimagebox").removeClass("checked");
       $(".addstorageimageonedrive").removeClass("checked");
    });
    $(".addstorageimagebox").click(function(){
         console.log("hi");
       $(".addstorageimagegoogle").removeClass("checked");
       $(".addstorageimagedropbox").removeClass("checked");
       $(".addstorageimagebox").addClass("checked");
       $(".addstorageimageonedrive").removeClass("checked");
    });
    $(".addstorageimageonedrive").click(function(){
         console.log("hi");
       $(".addstorageimagegoogle").removeClass("checked");
       $(".addstorageimagedropbox").removeClass("checked");
       $(".addstorageimagebox").removeClass("checked");
       $(".addstorageimageonedrive").addClass("checked");
    });
    });
    $(".googletab.tab-pane").click(function(){
         console.log("hi");
       $(".navtab-content-google").removeClass("hidden");
       $(".navtab-content-dropbox").addClass("hidden");
       $(".navtab-content-box").addClass("hidden");
       $(".navtab-content-onedrive").addClass("hidden");
    });
    $(".dropboxtab.tab-pane").click(function(){
         console.log("hi");
       $(".navtab-content-google").addClass("hidden");
       $(".navtab-content-dropbox").removeClass("hidden");
       $(".navtab-content-box").addClass("hidden");
       $(".navtab-content-onedrive").addClass("hidden");
    });
    $(".boxtab.tab-pane").click(function(){
         console.log("hi");
       $(".navtab-content-google").addClass("hidden");
       $(".navtab-content-dropbox").addClass("hidden");
       $(".navtab-content-box").removeClass("hidden");
       $(".navtab-content-onedrive").addClass("hidden");
    });
    $(".onedrivetab.tab-pane").click(function(){
         console.log("hi");
       $(".navtab-content-google").addClass("hidden");
       $(".navtab-content-dropbox").addClass("hidden");
       $(".navtab-content-box").addClass("hidden");
       $(".navtab-content-onedrive").removeClass("hidden");
    });

    // Header scroll class
    $(window).scroll(function(){
    if($(window).scrollTop() > 100)
    {
        $( "#header1" ).addClass( "header-scrolled" );
    }else{
        $( "#header1" ).removeClass( "header-scrolled" );
    }
   });
   //$(window).load(function() {
   $(document).ready(function(){
	if(window.location.pathname == '/login' || window.location.pathname == '/register' || window.location.pathname == '/logout'){
          console.log("inside clear");
          localStorage.clear();
        }
     var site = document.location.origin;
      console.log(site);
      var url = window.location.href;

      if(url != site+"/"){
        $( "#header1" ).addClass( "header-scrolled-overlay" );
        $( "#main-grid" ).addClass( "main-grid-overlay" );
      }else{ 
        $( "#header1" ).removeClass( "header-scrolled-overlay" );
        $( "#main-grid" ).removeClass( "main-grid-overlay" );
    }
});

      // Change hash for tab-pane
       var url = document.location.toString();
      	if (url.match('#')) {
      	    $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
      	} 

      	// Change hash for page-reload
      	$('.nav-tabs a').on('shown.bs.tab', function (e) {
      	    window.location.hash = e.target.hash;
      	});

        $('a[data-toggle="tab"]').on('click', function(e) {
          history.pushState(null, null, $(this).attr('href'));
        });
        
        // navigate to a tab when the history changes
        window.addEventListener("popstate", function(e) {
          var activeTab = $('[href=' + location.hash + ']');
          if (activeTab.length) {
            activeTab.tab('show');
          } else {
            $('.nav-tabs a:first').tab('show');
          }
        });
      $('#google').click(function(){
       console.log("google clicked");
       setTimeout(function(){ $($("#navtab-google").find('li')[0]).find('a').click();},1000);
      });
      $('#dropbox').click(function(){
       console.log("dropbox clicked");
       setTimeout(function(){ $($("#navtab-dropbox").find('li')[0]).find('a').click();},1000);
     });
      $('#box').click(function(){
       console.log("box clicked");
       setTimeout(function(){ $($("#navtab-box").find('li')[0]).find('a').click();},1000);
     });
      $('#onedrive').click(function(){
       console.log("onedrive clicked");
       setTimeout(function(){ $($("#navtab-onedrive").find('li')[0]).find('a').click();},1000);
     });

     var passphrase = document.getElementById("passphrase");
     passphrase.addEventListener("keydown", function (event) {
    if (event.keyCode === 13) {  //checks whether the pressed key is "Enter"
    }
  });
     var confirmpassphrase = document.getElementById("confirmpassphrase");
     confirmpassphrase.addEventListener("keydown", function (event) {
    if (event.keyCode === 13) {  //checks whether the pressed key is "Enter"
    }
  });
$(document).ready(function(){
	$.get("user/shares", function(data, status){
                                  var count=Object.keys(data).length;
                                  console.log("data iss"+count);
                $("#sharenotify,#sharenotify1").html('<i class="far fa-bell"></i><span class="badge">'+count+'</span>');
                $(".notification_header,.notification_header_mobile").html('<h3>You have '+count+' new notification');
	});
});

 });
