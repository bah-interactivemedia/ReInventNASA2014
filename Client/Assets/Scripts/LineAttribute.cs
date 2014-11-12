using UnityEngine;
using System.Collections;
using InControl;

public class LineAttribute : MonoBehaviour {

	// Use this for initialization
	void Start () {
	
	}
	
	// Update is called once per frame
	void Update () {
		var inputDevice = InputManager.ActiveDevice;

		this.transform.Rotate(new Vector3(0,0,inputDevice.RightTrigger.Value) * -40f * Time.deltaTime);
		this.transform.Rotate(new Vector3(0,0,inputDevice.LeftTrigger.Value) * 40f * Time.deltaTime);

		this.transform.Translate(new Vector3(1 * Time.deltaTime * inputDevice.LeftStickX, 1 * inputDevice.LeftStickY * Time.deltaTime, 0));

		this.transform.localScale += new Vector3(Time.deltaTime * inputDevice.RightStickY,0,0);
	}
}
