using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using Vectrosity;
using InControl;

public class Rectangle : MonoBehaviour {
	VectorLine rectLine;
	public Material lineMaterial;

	public GameController controller;

	List<Vector3> lines = new List<Vector3>();

	public Vector3 origin;
	private Vector2 size;

	// Use this for initialization
	void Start () {
		origin = new Vector3(0,0,1);
		size = new Vector2(.2f,.2f);
		rectLine = new VectorLine("Rectangle", new Vector3[8], lineMaterial, 2.0f); 
		rectLine.capLength = 8.0f;
		rectLine.Draw3DAuto();
	}

	void OnDestroy(){
		rectLine.StopDrawing3DAuto();
		VectorLine.Destroy(ref rectLine);
	}
	
	// Update is called once per frame
	void Update () {
		var inputDevice = InputManager.ActiveDevice;

		if (inputDevice.Action1.WasReleased){
			controller.endRectEdit();
		} else if (inputDevice.Action2.WasReleased){
			controller.cancelRectEdit();
		}

		origin = new Vector3(origin.x + (inputDevice.LeftStickX * Time.deltaTime),
		                     origin.y + (inputDevice.LeftStickY * Time.deltaTime),
		                     origin.z);

		var xSize = size.x + (inputDevice.RightStickX * Time.deltaTime);
		xSize = xSize < .01f ? .01f : xSize;

		var ySize = size.y - (inputDevice.RightStickY * Time.deltaTime);
		ySize = ySize < .01f ? .01f : ySize;

		size = new Vector2(xSize, ySize);

		rectLine.MakeRect (origin, new Vector3(origin.x + size.x, origin.y - size.y, origin.z));
	}
}
