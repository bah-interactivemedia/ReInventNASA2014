using UnityEngine;
using System.Collections;
using InControl;

public class LineAttribute : MonoBehaviour {
	public GameController controller;

	public Transform tl;
	public Transform br;
	
	// Update is called once per frame
	void Update () {
		var inputDevice = InputManager.ActiveDevice;

		if (inputDevice.Action1.WasReleased){
			controller.endLineEdit();
		} else if (inputDevice.Action2.WasReleased){
			controller.cancelLineEdit();
		}

		var zRotation = this.transform.rotation.eulerAngles.z;

		zRotation = zRotation > 180 ? zRotation - 360 : zRotation;

		if (zRotation > -90){
			this.transform.Rotate(new Vector3(0,0,inputDevice.RightTrigger.Value) * -40f * Time.deltaTime);
		}

		if (zRotation < 90){
			this.transform.Rotate(new Vector3(0,0,inputDevice.LeftTrigger.Value) * 40f * Time.deltaTime);
		}

		this.transform.Translate(new Vector3(1 * Time.deltaTime * inputDevice.LeftStickX, 1 * inputDevice.LeftStickY * Time.deltaTime, 0));

		if (inputDevice.RightStickY > 0 || this.transform.localScale.x > 0.05f){
			this.transform.localScale += new Vector3(Time.deltaTime * inputDevice.RightStickY,0,0);
		}

//		TestEdges ();
	}

	void TestEdges () {
		tl.transform.position = TopLeft ();
		br.transform.position = BottomRight ();
	}

	public Vector3 TopLeft() {
		Vector3 p = transform.position;
		var zRotation = this.transform.rotation.eulerAngles.z * Mathf.Deg2Rad;
		var xScale = this.transform.localScale.x;
		var o = new Vector3 (Mathf.Cos (zRotation), Mathf.Sin(zRotation), 0) * -xScale/2;
		return p + o;
	}

	public Vector3 BottomRight() {
		Vector3 p = transform.position;
		var zRotation = this.transform.rotation.eulerAngles.z * Mathf.Deg2Rad;
		var xScale = this.transform.localScale.x;
		var o = new Vector3 (Mathf.Cos (zRotation), Mathf.Sin(zRotation), 0) * xScale/2;
		return p + o;
	}
}
