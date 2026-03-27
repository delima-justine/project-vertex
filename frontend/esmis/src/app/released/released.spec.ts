import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Released } from './released';

describe('Released', () => {
  let component: Released;
  let fixture: ComponentFixture<Released>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Released]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Released);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
