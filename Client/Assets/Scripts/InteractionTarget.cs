using UnityEngine;
using System.Collections;
using InControl;

public class InteractionTarget : MonoBehaviour {

	public Transform player;

	// Use this for initialization
	void Start () {
		LookAtPlayer();
	}

	// Update is called once per frame
	void Update () {

	}

	// Might have to delete this for levi's code later
	void LookAtPlayer(){
		this.transform.LookAt(player);
	}
}
