<?php 

namespace App\Providers;
/*-----------------------------------------------------------------------------
Structure PRE stores the public components (p, q, g) which are used by all the 
algorithms. The members implement the various algorithms of the PRE and all 
related functions.
-> q divides p-1
-> g is the generator
-----------------------------------------------------------------------------*/
class PRE
{
	private $q;
	private $p;
	private $g;
	
	/*-----------------------------------------------------------------------------
	Function to perform XOR operation. (CAUTION: Custom XOR function, reduces the 
	first input	to the size of the second input)
	-----------------------------------------------------------------------------*/
	public function xorAll($inpa,$inpb)
	{
		$bitlena = strlen($inpa);
		$bitlenb = strlen($inpb);
		if ($bitlena > $bitlenb)
		{
			$bitlen = $bitlenb;
		}
		else if ($bitlenb > $bitlena)
		{
			$bitlen = $bitlenb;
		}
		else
		{
			$bitlen = $bitlena;
		}
		$bitlen = 146;
		$ai_bits = mb_strimwidth($inpa, 0, $bitlen, '');
		$bi_bits = mb_strimwidth($inpb, 0, $bitlen, '');
		$output = gmp_xor(gmp_init($ai_bits,16),gmp_init($bi_bits,16));
		return gmp_strval($output,16);
	}
	/*-----------------------------------------------------------------------------
	Function to setup 
	-----------------------------------------------------------------------------*/
	public function Setup($kappa)
	{
		error_log( "PRE Setup<br>");
		$this->gen_Safeprimes($kappa);
	}
	/*-----------------------------------------------------------------------------
	Function to generate safe primes
	-----------------------------------------------------------------------------*/
	public function gen_Safeprimes($kappa)
	{
		$GEN = 0;
		if ($GEN == 1)
		{
			$iterate = 1;
			while($iterate == 1)
			{
				$seed = gmp_random_bits(1024);
				$this->q = gmp_nextprime($seed);
				for($i = 2; $i < 100; $i++)
				{
					if( gmp_prob_prime(gmp_add(gmp_mul($this->q, $i), 1)))
					{
						$iterate = 0;
						$this->p = gmp_add(gmp_mul($this->q, $i), 1);				
					}
				}
			}
		}
		else
		{	
			$params_File = fopen(app_path()."/Providers/Params.txt", "r") or die("Unable to open file!");
			$p = gmp_init(fgets($params_File), 16);
			$q = gmp_init(fgets($params_File), 16);
			$g = gmp_init(fgets($params_File), 16);
			fclose($params_File);
			$this->q = $q;
			$this->p = $p;
			$this->g = $g;
		}
	}
	/*-----------------------------------------------------------------------------
	Function to generate the generator
	-----------------------------------------------------------------------------*/
	public function gen_Primate($kappa)
	{
		$this->g = null;
		while($this->g == null)
		{
			$g = gmp_random_bits($kappa);
			if(gmp_powm($g, $this->q, $this->p))
			{
				$this->g = $g;
				error_log( "<br> g is: ". $this->g . "<br>");
			}
			else
			{
				error_log( " Primate element not generated !!! ");
			}
		}
	}
	/*-----------------------------------------------------------------------------
	Function to generate keys for users
	-----------------------------------------------------------------------------*/
	public function KeyGen($kappa)
	{
		$sk_1 = gmp_random_bits($kappa);
		$sk_2 = gmp_random_bits($kappa);
		$pk_1 = gmp_powm($this->g, $sk_1, $this->p);
		$pk_2 = gmp_powm($this->g, $sk_2, $this->p);
		$Keyobj = new key_Struct();
		$Keyobj->sk_1 = $sk_1;
		$Keyobj->sk_2 = $sk_2;
		$Keyobj->pk_1 = $pk_1;
		$Keyobj->pk_2 = $pk_2;
		
		return $Keyobj;
	}
	/*-----------------------------------------------------------------------------
	ExtractSecKey function. Input is a key_Struct object. 
	Returns a secretkey_Struct object which has only the secret keys of the user.
	-----------------------------------------------------------------------------*/
	public function ExtractSecKey($Keyobj)
	{
		$secKeyobj = new secretkey_Struct();
		$secKeyobj->sk_1 = $Keyobj->sk_1;
		$secKeyobj->sk_2 = $Keyobj->sk_2;
		
		return $secKeyobj;
	}
	/*-----------------------------------------------------------------------------
	ExtractPubKey function. Input is a key_Struct object. 
	Returns a publickey_Struct object which has only the secret keys of the user.
	-----------------------------------------------------------------------------*/	
	public function ExtractPubKey($Keyobj)
	{
		$pubKeyobj = new publickey_Struct();	
		$pubKeyobj->pk_1 = $Keyobj->pk_1;
		$pubKeyobj->pk_2 = $Keyobj->pk_2;
		
		return $pubKeyobj;
	}
	/*-----------------------------------------------------------------------------
	ReKeyGen function. 
	Input is secret_Keyobj of delegator i, public_Keyobj of the delegator i and 
	the public_Keyobj of the delegatee j. 
	Returns a rekey_Struct object which us used to re-encrypt from i to j.
	-----------------------------------------------------------------------------*/		
	public function ReKeyGen($kappa, $secret_Keyobj_i, $public_Keyobj_i, $public_Keyobj_j)
	{
		$rekey_Keyobj = new rekey_Struct();
		$ski_1 = $secret_Keyobj_i->sk_1;
		$ski_2 = $secret_Keyobj_i->sk_2;
		$pki_1 = $public_Keyobj_i->pk_1;
		$pki_2 = $public_Keyobj_i->pk_2;
		$pkj_1 = $public_Keyobj_j->pk_1;
		$pkj_2 = $public_Keyobj_j->pk_2;
		
		$h = gmp_random_bits(256);
		$pi = gmp_random_bits(256);
		$v = hash('sha256', gmp_strval($h,16) . gmp_strval($pi,16));
		$v = gmp_init($v, 16);
		$V = gmp_powm($pkj_2, $v, $this->p);
		$t1 = gmp_powm($this->g, $v, $this->p);
		$inpa = gmp_strval($this->HashBlowup($t1),16);
		$inpb = gmp_strval($pi,16) . '303131313131313130' . gmp_strval($h,16);
		$W = $this->xorAll($inpa,$inpb);
		$t2 = hash('sha256', gmp_strval($pki_2,16));
		$t2 = gmp_init($t2, 16);
		$t3 = gmp_add(gmp_mul($ski_1, $t2), $ski_2);
		$t4 = gmp_invert($t3, $this->q);
		$RKi_j = gmp_mul($h, $t4);
		$RKi_j = gmp_mod($RKi_j, $this->q);
		$rekey_Keyobj->RKi_j = $RKi_j;
		$rekey_Keyobj->V = $V;
		$rekey_Keyobj->W = $W;
		
		return $rekey_Keyobj;
	}
	/*-----------------------------------------------------------------------------
	HashBlowup function. 
	Input is string. output is a blown up hash output. 
	CAUTION: To be customized later.
	-----------------------------------------------------------------------------*/	
	public function HashBlowup($inp)
	{
		$inp = gmp_init(gmp_strval($inp,16),16);
		$ONE = gmp_init("0x1");
		
		$t511 = gmp_add($inp, $ONE);
		$t511 = hash('sha256', gmp_strval($t511,16));
		$t511 = gmp_init(($t511), 16);
		$t521 = gmp_init(gmp_strval($t511,16),16);

		$t512 = gmp_add($t521, $ONE);
		$t512 = hash('sha256', gmp_strval($t512,16));
		$t512 = gmp_init(($t512), 16);
		$t522 = gmp_init(gmp_strval($t512,16),16);
		
		$t513 = gmp_add($t522, $ONE);
		$t513 = hash('sha256', gmp_strval($t513,16));
		$t513 = gmp_init(($t513), 16);
		$t523 = gmp_init(gmp_strval($t513,16),16);
						
		$t514 = gmp_add($t523, $ONE);
		$t514 = hash('sha256', gmp_strval($t514,16));
		$t514 = gmp_init(($t514), 16);
		$t524 = gmp_init(gmp_strval($t514,16),16);
		
		$inpa = gmp_init(gmp_strval($t521,16) . gmp_strval($t522,16) . gmp_strval($t523,16) . gmp_strval($t524,16),16);

		return $inpa;
	}
	/*-----------------------------------------------------------------------------
	Encryption function. 
	Input is public_Keyobj_i of user i and the message m. 
	Output is a first level ciphertext for user i.
	-----------------------------------------------------------------------------*/		
	public function Encrypt1($public_Keyobj_i, $m)
	{
		$ct1_CTobj = new ct1_Struct();
		$pki_1 = $public_Keyobj_i->pk_1;
		$pki_2 = $public_Keyobj_i->pk_2;
		
		$u = gmp_random_bits(128);
		$t1 = hash('sha256', gmp_strval($pki_2,16));
		$t1 = gmp_init($t1, 16);
		$t2 = gmp_powm($pki_1, $t1, $this->p);
		$t3 = gmp_mul($t2, $pki_2);
		$t4 = gmp_mod($t3, $this->p);

		$D = gmp_powm($t4, $u, $this->p);

		$w = gmp_random_bits(128);
		$w = gmp_strval($w,10);
		$r = hash('sha256', gmp_strval($m,16) . gmp_strval($w,16));
		$r = gmp_init($r, 16);

		$E = gmp_powm($t4, $r, $this->p);

		$t5 = gmp_powm($this->g, $r, $this->p);
		$inpa = gmp_strval($this->HashBlowup($t5),16);
		$inpb = gmp_strval($w,16) . '303131313131313130' . gmp_strval($m,16);

		$F = $this->xorAll($inpa,$inpb);
		
		$t6 = gmp_mul($r, gmp_init(hash('sha256', gmp_strval($D,16) .gmp_strval($E,16).$F ), 16));
		$t7 = gmp_add($t6, $u);

		$s = gmp_mod($t7, $this->q);

		$ct1_CTobj->level = 1;
		$ct1_CTobj->D = $D;
		$ct1_CTobj->E = $E;
		$ct1_CTobj->F = $F;
		$ct1_CTobj->s = $s;

		return $ct1_CTobj;
	}
	/*-----------------------------------------------------------------------------
	Re-Encryption function. 
	Input is rekey from i to j, first level ciphertext, 
		key objects of delegatee and delegator. 
	Output is a second level ciphertext for user j.
	-----------------------------------------------------------------------------*/	
	public function ReEncrypt($rekey_Keyobj, $ct1_CTobj, $Keyobj_i, $Keyobj_j)
	{
		$ct2_CTobj = new ct2_Struct();
		$pki_1 = $Keyobj_i->pk_1;
		$pki_2 = $Keyobj_i->pk_2;
		
		$pkj_1 = $Keyobj_j->pk_1;
		$pkj_2 = $Keyobj_j->pk_2;
		
		$RKi_j = $rekey_Keyobj->RKi_j;
		$V = $rekey_Keyobj->V;
		$W = $rekey_Keyobj->W;
		
		$D = $ct1_CTobj->D;
		$E = $ct1_CTobj->E;
		$F = $ct1_CTobj->F;
		$s = $ct1_CTobj->s;
		$t1 = hash('sha256', gmp_strval($pki_2,16));
		$t1 = gmp_init($t1, 16);
		$t2 = gmp_powm($pki_1, $t1, $this->p);
		$t3 = gmp_mul($t2, $pki_2);
		$t4 = gmp_mod($t3, $this->p);
		$temp = $t4;
		$LHS = gmp_powm($t4, $s, $this->p);
		
		$t5 =  gmp_init(hash('sha256', gmp_strval($D,16).gmp_strval($E,16).gmp_strval($F,16)), 16);
		$t6 = gmp_powm($E, $t5, $this->p);
		$RHS = gmp_mul($D,$t6);
		$RHS = gmp_mod($RHS, $this->p);
		
                error_log(print_r($RHS,true));
                error_log(print_r($LHS,true));
	

	
		if (gmp_cmp($LHS,$RHS) == 0)
		{
			$ct2_CTobj->level = 2;
			$ct2_CTobj->E1 = gmp_powm($E,$RKi_j,$this->p);
			$ct2_CTobj->F = $F;
			$ct2_CTobj->V = $V;
			$ct2_CTobj->W = $W;
			
			return $ct2_CTobj;
		}
		else
		{
			error_log( "Invalid first level Ciphertext <br> ");
			return "Invalid First level Cihertext - Re-enc not possible";
		}
	}
	/*-----------------------------------------------------------------------------
	Decryption function. 
	Input is secret key and public key of the receiver and the ciphertext, 
	Output is the message.
	-----------------------------------------------------------------------------*/	
	public function Decrypt($secret_Keyobj_i, $public_Keyobj_i, $ct_CTobj)
	{
		$pki_1 = $public_Keyobj_i->pk_1;
		$pki_2 = $public_Keyobj_i->pk_2;
		$ski_1 = $secret_Keyobj_i->sk_1;
		$ski_2 = $secret_Keyobj_i->sk_2;
		
		if($ct_CTobj->level == 1)
		{
			$t1 = hash('sha256', gmp_strval($pki_2,16));
			$t1 = gmp_init($t1, 16);
			$t2 = gmp_powm($pki_1, $t1, $this->p);
			$t3 = gmp_mul($t2, $pki_2);
			$t4 = gmp_mod($t3, $this->p);
			$temp = $t4;
			$LHS = gmp_powm($t4, $ct_CTobj->s, $this->p);

			$t5 =  gmp_init(hash('sha256', gmp_strval($ct_CTobj->D,16).gmp_strval($ct_CTobj->E,16).$ct_CTobj->F), 16);
			$t6 = gmp_powm($ct_CTobj->E, $t5, $this->p);
			$RHS = gmp_mul($ct_CTobj->D,$t6);
			$RHS = gmp_mod($RHS, $this->p);

			if (gmp_cmp($LHS,$RHS) == 0)
			{
				$t1 = hash('sha256', gmp_strval($pki_2,16));
				$t1 = gmp_init($t1, 16);
				$t2 = gmp_mul($ski_1, $t1);
				$t3 = gmp_add($t2, $ski_2);
				$t4 = gmp_mod($t3, $this->q);
				$t5 = gmp_invert($t4, $this->q);
				$t6 = gmp_powm($ct_CTobj->E, $t5, $this->p);
				$mw = $this->xorAll(gmp_strval($this->HashBlowup($t6),16),$ct_CTobj->F);
				$pieces = explode("303131313131313130", $mw);
				$m = $pieces[1];
				$w = $pieces[0];
				$r = hash('sha256', $m . $w);
				$r = gmp_init($r, 16);
		
				if(gmp_cmp(gmp_powm($temp, $r, $this->p),$ct_CTobj->E) == 0) 
				{
					return $m;				
				}
				else
				{
					error_log( "Invalid Ciphertext 2 <br>");
					return "Invalid Ciphertext: Failed test 2";
				}
			}
			else
			{
				error_log( "Invalid Ciphertext 1 <br> ");
				return "Invalid Ciphertext: Failed test 1";
			}
		}
		else if($ct_CTobj->level == 2)
		{
			$E1 = $ct_CTobj->E1;
			$F = $ct_CTobj->F;
			$V = $ct_CTobj->V;
			$W = $ct_CTobj->W;

			$t1 = gmp_invert($ski_2, $this->q);
			$t2 = gmp_powm($V, $t1, $this->p);
			$inpa = gmp_strval($this->HashBlowup($t2),16);

			$hpi = $this->xorAll($inpa, $W);
			$pieces = explode("303131313131313130", $hpi);
			$h = $pieces[1];
			$pi = $pieces[0];
			$t3 = gmp_invert(gmp_init($h,16), $this->q);
			$t4 = gmp_powm($E1, $t3, $this->p);		
			$mw = $this->xorAll(gmp_strval($this->HashBlowup($t4),16),$ct_CTobj->F);
			
			$pieces = explode("303131313131313130", $mw);
			$m = $pieces[1];
			$w = $pieces[0];
			$v1 = hash('sha256', $h.$pi);
			$v1 = gmp_init($v1, 16);
			$V1 = gmp_powm($pki_2, $v1, $this->p);
			$w1 = hash('sha256', $m.$w);
			$w1 = gmp_init($w1, 16);
			$W1 = gmp_powm($this->g, $w1, $this->p);
			$E11 = gmp_powm($W1, gmp_init($h,16), $this->p);

			if(!((gmp_cmp($V, $V1) == 0) && (gmp_cmp($E1, $E11) == 0)))
			{
				return $m;
			}
			else 
			{
				error_log( "Invalid second level Ciphertext <br> ");
				return "Invalid second level Ciphertext <br> ";
			}
		}
	}
 	/*-----------------------------------------------------------------------------
	Function to print ciphertext
	-----------------------------------------------------------------------------*/		
    public function printCipher($ct_CTobj) 
	{
		if ($ct_CTobj->level == 1)
		{
			error_log( "First level Ciphertext <br>");
			error_log( "D = " . $ct_CTobj->D . "<br>");
			error_log( "E = " . $ct_CTobj->E . "<br>");
			error_log( "F = " . $ct_CTobj->F . "<br>");
			error_log( "s = " . $ct_CTobj->s . "<br>");
		}
		else if($ct_CTobj->level == 2)
		{
			error_log( "Second level Ciphertext <br>");
			error_log( "E1 = " . $ct_CTobj->E1 . "<br>");
			error_log( "F = " . $ct_CTobj->F . "<br>");
			error_log( "V = " . $ct_CTobj->V . "<br>");
			error_log( "W = " . $ct_CTobj->W . "<br>");
		}
	}
}







?>
