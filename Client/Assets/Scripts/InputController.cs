using UnityEngine;
using System.Collections;
using InControl;

public class InputController : MonoBehaviour {
	public Transform leftEye;

	// Use this for initialization
	void Start () {
	
	}
	
	// Update is called once per frame
	void Update () {
		var inputDevice = InputManager.ActiveDevice;
		if (inputDevice.AnyButton){
			Debug.Log("BUTTON!!!");
		}
		if (inputDevice.RightTrigger.WasPressed){
			Debug.Log("Trigger Pressed!");

			Ray ray = leftEye.camera.ViewportPointToRay(new Vector3(0.5F, 0.5F, 0));
			RaycastHit hit;
			if (Physics.Raycast(ray, out hit) && hit.transform.tag == "Image"){
				print("I'm targeting at " + hit.transform.name);
			}
		}
	}
}
