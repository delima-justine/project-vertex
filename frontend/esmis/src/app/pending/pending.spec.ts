import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Pending } from './pending';

describe('Pending', () => {
  let component: Pending;
  let fixture: ComponentFixture<Pending>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Pending]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Pending);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
